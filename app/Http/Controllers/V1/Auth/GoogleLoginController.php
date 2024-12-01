<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\V1\Auth\AuthRepository;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Google auth redirect
     *
     * @return RedirectResponse
     * @group Auth
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google auth callback
     *
     * @param AuthRepository $repository
     * @return RedirectResponse
     * @group Auth
     */
    public function authenticate(AuthRepository $repository): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $id = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName();

        if (!$repository->validateGoogleAuthResponse($name, $email)) {
            return redirect(config('app.spa_url') . '/login');
        }

        $user = $repository->createOrUpdateUserFromSocialite($id, $email, $name);

        Auth::login($user);

        return redirect(config('app.spa_url') . '/dashboard');
    }
}
