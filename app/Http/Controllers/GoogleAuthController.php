<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\StudentFaculty;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Restrict to @lccdo.edu.ph emails
        if (!str_ends_with($googleUser->getEmail(), '@lccdo.edu.ph')) {
            return redirect('/')->with('error', 'Only LCCDO emails are allowed.');
        }

        // Create or get user
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            ['name' => $googleUser->getName(), 'password' => bcrypt(str()->random(16))]
        );

        // Derive first and last name from email
        $email = $googleUser->getEmail();
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
        $defaultSchoolId = 'S' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
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

        // If profile incomplete → go to profile completion form
        if (!$user->studentFaculty->course || !$user->studentFaculty->birthdate) {
            return redirect()->route('profile.complete');
        }

        return redirect('/');
    }
}

