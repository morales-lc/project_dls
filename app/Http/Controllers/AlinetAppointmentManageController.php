<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AlinetAppointmentAccepted;
use App\Mail\AlinetAppointmentRejected;

class AlinetAppointmentManageController extends Controller
{
    public function index()
    {
        $status = request('status');
        $q = request('q');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $service = request('service');
        $perPage = (int) request('per_page', 15);
        if ($perPage < 5) { $perPage = 5; }
        if ($perPage > 100) { $perPage = 100; }

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

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'reason' => 'nullable|string|max:5000',
        ]);
        $appointment = AlinetAppointment::findOrFail($id);
        $appointment->status = $request->status;

        // If accepting an Onsite request, set appointment_date to the upcoming Saturday (next Saturday if today is Sunday)
    if ($request->status === 'accepted' && (stripos($appointment->mode_of_research, 'Onsite') !== false)) {
            $tz = 'Asia/Manila';
            $today = \Carbon\Carbon::now($tz)->startOfDay();
            $dow = (int) $today->dayOfWeek; // 0=Sun..6=Sat
            $daysAhead = ($dow === 0) ? 6 : (6 - $dow);
            $appointment->appointment_date = $today->copy()->addDays($daysAhead)->toDateString();
        }
        $appointment->save();

        $guestUser = null;
        // If accepting an Online request, create or update a temporary guest account with 7-day expiration
        if ($request->status === 'accepted' && (stripos($appointment->mode_of_research, 'Online') !== false)) {
            // Check if any user already exists for this email
            $existingUser = \App\Models\User::where('email', $appointment->email)->first();
            
            // Generate random password
            $plainPassword = \Illuminate\Support\Str::random(10);
            
            // Build full name from appointment fields
            $fullName = trim(($appointment->prefix ? $appointment->prefix . ' ' : '') . 
                            $appointment->firstname . ' ' . 
                            $appointment->lastname);

            if ($existingUser) {
                if ($existingUser->role === 'guest') {
                    // Update existing guest user: extend expiration, reactivate, and reset password
                    $existingUser->name = $fullName;
                    $existingUser->password = \Illuminate\Support\Facades\Hash::make($plainPassword);
                    $existingUser->guest_plain_password = \Illuminate\Support\Facades\Crypt::encryptString($plainPassword);
                    $existingUser->guest_expires_at = \Carbon\Carbon::now('Asia/Manila')->addDays(7);
                    $existingUser->guest_account_status = 'active';
                    $existingUser->save();
                    $guestUser = $existingUser;
                } else {
                    // Email belongs to a regular/admin user - skip guest account creation
                    // The email will still be sent but without guest credentials
                    Log::info('Skipping guest account creation - email belongs to existing non-guest user', [
                        'email' => $appointment->email,
                        'existing_role' => $existingUser->role,
                    ]);
                    $guestUser = null;
                }
            } else {
                // Generate a username derived from email (ensure uniqueness)
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
                
                // Create new guest user account
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

        // Send email to requester using Mailable classes
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
