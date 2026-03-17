<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AlinetAppointmentAccepted;
use App\Mail\AlinetAppointmentRejected;

/**
 * ALINET Appointment Management Controller
 * 
 * Manages ALINET (Alumni and Library Information Network) appointment requests
 * from external users. Handles appointment approval/rejection and automated
 * guest account creation for online access requests.
 * 
 * @package App\Http\Controllers
 */
class AlinetAppointmentManageController extends Controller
{
    /**
     * Display a paginated list of ALINET appointments with filtering
     * 
     * Supports filtering by status, service type, date range, and search query.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get filter parameters from request
        $status = request('status');
        $q = request('q');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $service = request('service');
        $perPage = (int) request('per_page', 15);
        if ($perPage < 5) { $perPage = 5; }
        if ($perPage > 100) { $perPage = 100; }

        // Build query with filters and pagination
        $appointments = AlinetAppointment::query()
            ->status($status)
            ->service($service)
            ->dateBetween($dateFrom, $dateTo)
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(request()->query());

        return view('alinet.manage', compact('appointments', 'status', 'q', 'dateFrom', 'dateTo', 'service', 'perPage'));
    }

    /**
     * Update the status of an ALINET appointment (accept/reject)
     * 
     * Handles:
     * - Setting appointment date for onsite requests (next Saturday)
     * - Creating/updating temporary guest accounts for online requests (7-day expiration)
     * - Sending acceptance/rejection emails to applicants
     * - Generating random passwords for guest accounts
     * - Preventing duplicate accounts for existing users
     * 
     * @param Request $request HTTP request with status and optional reason
     * @param int $id Appointment ID
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'reason' => 'nullable|string|max:5000',
        ]);
        $appointment = AlinetAppointment::findOrFail($id);
        $appointment->status = $request->status;

        // For accepted onsite requests, calculate the next Saturday appointment date
        // If today is Sunday, schedule for next Saturday (6 days ahead)
    if ($request->status === 'accepted' && (stripos($appointment->mode_of_research, 'Onsite') !== false)) {
            $tz = 'Asia/Manila';
            $today = \Carbon\Carbon::now($tz)->startOfDay();
            $dow = (int) $today->dayOfWeek; // 0=Sun..6=Sat
            $daysAhead = ($dow === 0) ? 6 : (6 - $dow);
            $appointment->appointment_date = $today->copy()->addDays($daysAhead)->toDateString();
        }
        $appointment->save();

        $guestUser = null;
        
        // For accepted online requests, create or update a temporary guest account
        // Guest accounts expire after 7 days and have limited access to resources
        if ($request->status === 'accepted' && (stripos($appointment->mode_of_research, 'Online') !== false)) {
            // Check if any user already exists for this email to prevent duplicates
            $existingUser = \App\Models\User::where('email', $appointment->email)->first();
            
            // Generate a secure random password for the guest account
            // This will be encrypted and sent via email
            $plainPassword = \Illuminate\Support\Str::random(10);
            
            // Build full name from appointment fields
            $fullName = trim(($appointment->prefix ? $appointment->prefix . ' ' : '') . 
                            $appointment->firstname . ' ' . 
                            $appointment->lastname);

            if ($existingUser) {
                if ($existingUser->role === 'guest') {
                    // Reactivate existing guest account: extend expiration to 7 more days,
                    // reset status to active, and generate new password
                    $existingUser->name = $fullName;
                    $existingUser->password = \Illuminate\Support\Facades\Hash::make($plainPassword);
                    $existingUser->guest_plain_password = \Illuminate\Support\Facades\Crypt::encryptString($plainPassword);
                    $existingUser->guest_expires_at = \Carbon\Carbon::now('Asia/Manila')->addDays(7);
                    $existingUser->guest_account_status = 'active';
                    $existingUser->save();
                    $guestUser = $existingUser;
                } else {
                    // Security measure: Don't create guest accounts for existing staff/student users
                    // This prevents privilege escalation or account conflicts
                    Log::info('Skipping guest account creation - email belongs to existing non-guest user', [
                        'email' => $appointment->email,
                        'existing_role' => $existingUser->role,
                    ]);
                    $guestUser = null;
                }
            } else {
                // Generate a unique username based on the email local part
                // Add numeric suffix if username already exists
                $email = (string) $appointment->email;
                $baseUsername = $email && str_contains($email, '@')
                    ? explode('@', $email)[0]
                    : 'guest';
                $baseUsername = preg_replace('/[^A-Za-z0-9_\.\-]/', '_', $baseUsername) ?: 'guest';
                $candidate = $baseUsername;
                $suffix = 1;
                while (\App\Models\User::where('username', $candidate)->exists()) {
                    $candidate = $baseUsername . '_' . $suffix;
                    $suffix++;
                    if ($suffix > 9999) { // safety break
                        $candidate = $baseUsername . '_' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(5));
                        break;
                    }
                }
                
                // Create new guest user account with 7-day expiration
                // Password is both hashed (for authentication) and encrypted (for email)
                $guestUser = \App\Models\User::create([
                    'name' => $fullName,
                    'email' => $appointment->email,
                    'username' => $candidate,
                    'password' => \Illuminate\Support\Facades\Hash::make($plainPassword),
                    'role' => 'guest',
                    'guest_plain_password' => \Illuminate\Support\Facades\Crypt::encryptString($plainPassword),
                    'guest_expires_at' => \Carbon\Carbon::now('Asia/Manila')->addDays(7),
                    'guest_account_status' => 'active',
                ]);
            }
        }

        // Send acceptance or rejection email notification to the applicant
        // Includes login credentials for online access if applicable
        try {
            if ($request->status === 'accepted') {
                Log::info('Attempting to send acceptance email', [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email,
                    'mode' => $appointment->mode_of_research,
                    'has_guest_user' => $guestUser !== null,
                ]);
                Mail::to($appointment->email)->send(new AlinetAppointmentAccepted($appointment, $guestUser));
                Log::info('Acceptance email sent successfully', ['appointment_id' => $appointment->id]);
            } else {
                Log::info('Attempting to send rejection email', [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email,
                ]);
                Mail::to($appointment->email)->send(new AlinetAppointmentRejected($appointment, $request->input('reason')));
                Log::info('Rejection email sent successfully', ['appointment_id' => $appointment->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ALINET email', [
                'appointment_id' => $appointment->id,
                'status' => $request->status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Continue with redirect even if email fails
        }

        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Appointment status updated and email sent.')
            : redirect()->back()->with('success', 'Appointment status updated and email sent.');
    }
}
