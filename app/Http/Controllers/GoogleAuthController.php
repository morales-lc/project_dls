<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\StudentFaculty;
use App\Models\UserLoginLog;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        // Check if user cancelled or an error occurred
        if (request()->has('error')) {
            return redirect()->route('login')->with('error', 'Google sign-in was cancelled or failed. Please try again.');
        }

        $googleUser = Socialite::driver('google')->user();

        // Restrict to @lccdo.edu.ph emails
        $googleEmail = strtolower($googleUser->getEmail());
        if (!str_ends_with($googleEmail, '@lccdo.edu.ph')) {
            return redirect('/')->with('error', 'Only @lccdo emails are allowed.');
        }

        // Helper: normalize local-part for alias matching (remove dots and plus tag) for our domain
        $normalizeLocal = function (string $email): array {
            $email = strtolower($email);
            [$local, $domain] = explode('@', $email, 2);
            if ($domain === 'lccdo.edu.ph') {
                // Remove dot aliases and plus addressing
                $local = str_replace('.', '', $local);
                $local = preg_replace('/\+.*/', '', $local);
            }
            return [$local, $domain];
        };

        // Try exact match first
        $user = User::whereRaw('LOWER(email) = ?', [$googleEmail])->first();
        $createdNow = false;

        // If not found, try alias-equivalent match within domain by normalized local-part
        if (!$user) {
            [$gLocal, $gDomain] = $normalizeLocal($googleEmail);
            $candidates = User::whereRaw('LOWER(email) LIKE ?', ['%@' . $gDomain])->get();
            foreach ($candidates as $cand) {
                [$cLocal, $cDomain] = $normalizeLocal($cand->email);
                if ($cLocal === $gLocal && $cDomain === $gDomain) {
                    $user = $cand;
                    break;
                }
            }
        }

        // If still not found, create the user now (Google-first account)
        if (!$user) {
            $user = new User();
            $user->email = $googleEmail;
            $user->name = $googleUser->getName();
            // Optional: set username to normalized local-part if vacant
            [$localPart] = $normalizeLocal($googleEmail);
            $user->username = $localPart;
            $user->password = bcrypt(str()->random(16));
            $user->save();
            $createdNow = true;
        }

        // Derive first and last name from email
        $email = $googleEmail;
        $localPart = explode('@', $email)[0];
        $nameParts = explode('.', $localPart);
        $firstName = isset($nameParts[0]) ? ucfirst($nameParts[0]) : '';
        $lastName = isset($nameParts[1]) ? ucfirst($nameParts[1]) : '';

        // Derive profile picture from email if Google avatar is not available
        $profilePicture = $googleUser->getAvatar();
        if (!$profilePicture) {
            $profilePicture = 'https://ui-avatars.com/api/?name=' . urlencode($firstName . ' ' . $lastName) . '&background=6c63ff&color=fff';
        }

        // Ensure student_faculty record exists
        // Generate a default school_id (e.g., S + user_id padded)
        $defaultSchoolId = 'C' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
        StudentFaculty::firstOrCreate(
            ['user_id' => $user->id],
            [
                'school_id' => $defaultSchoolId,
                'profile_picture' => $profilePicture,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]
        );

        Auth::login($user);

        // Track student/faculty Google sign-ins for login analytics.
        UserLoginLog::recordForUser($user, request());

        // Only prompt profile completion for brand-new Google-created accounts
        if ($createdNow) {
            $sf = $user->studentFaculty; // just created above if missing
            if (!$sf || !$sf->course || !$sf->birthdate) {
                return redirect()->route('profile.complete');
            }
        }

        return redirect('/');
    }
}

