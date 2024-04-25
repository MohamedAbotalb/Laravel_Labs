<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GithubController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $user = Socialite::driver('github')->user();
        $githubUser = User::updateOrCreate([
            'github_id' => $user->id
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'password' => bcrypt(request(Str::random())) // Set some random password
        ]);

        // Log in the new or updated user.
        auth()->login($githubUser, true);

        // Redirect to url as requested by user, if empty use /dashboard page as generated by Jetstream
        return redirect()->intended('/posts');
    }
}