<?php

namespace App\Http\Repositories\V1\App;

use App\Models\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AppRepository
{
    private function generatePrivateKey(): string
    {
        return Str::random(32) . '-' . Str::uuid();
    }

    public function createApp(array $data): App
    {
        /** @var App $app */
        $app = App::create(array_merge($data, [
            'private_key' => $this->generatePrivateKey(),
            'owner_id' => Auth::id()
        ]));

        $app->users()->attach(Auth::id(), [
            'role' => 'owner',
        ]);

        return $app;
    }

    public function cyclePrivateKey(App $app): void
    {
        $app->update(['private_key' => $this->generatePrivateKey()]);
    }

    public function updateApp(App $app, array $data): App
    {
        $app->update($data);

        return $app->refresh();
    }

    public function deleteApp(App $app): void
    {
        $app->delete();
    }
}
