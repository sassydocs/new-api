<?php

namespace App\Http\Repositories\V1\Auth;

use App\Mail\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthRepository
{
    public function validateGoogleAuthResponse($name, $email): bool
    {
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
        ], [
            'email' => ['required', 'string', 'email'],
            'name' => ['required', 'string'],
        ]);

        return $validator->passes();
    }

    public function createUser(array $data, bool $setLastLogin = true): User
    {
        // if a password was not provided, generate one at random
        if (! array_key_exists('password', $data)) {
            $data['password'] = Hash::make(Str::random(24));
        }

        if ($setLastLogin) {
            $data['last_login_at'] = now();
        }

        $user = User::create($data);

        $message = (new UserCreated($user))
            ->onQueue('emails');

        Mail::to($user)
            ->queue($message);

        return $user;
    }

    public function createOrUpdateUserFromSocialite(string $google_id, string $email, string $name): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = $this->createUser([
                'name' => $name,
                'google_id' => $google_id,
                'email' => $email,
            ]);
        } else {
            $user->update([
                'google_id' => $google_id,
                'last_login_at' => now(),
            ]);
        }

        return $user;
    }
}
