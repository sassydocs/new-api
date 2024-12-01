<?php

namespace Database\Factories;

use App\Http\Enum\V1\UserTypeEnum;
use App\Models\App;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\App>
 */
class AppFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'private_key' => Str::upper(Str::random(12) . '-' . Str::uuid()),
            'owner_id' => User::factory()->create(),
        ];
    }

    public function owner(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $user->id,
        ])->afterCreating(function (App $app) use ($user) {
            $app->users()->attach($user, [
                'role' => UserTypeEnum::Owner->value,
            ]);
        });
    }

    public function admin(User $user): static
    {
        return $this->afterCreating(function (App $app) use ($user) {
            $app->users()->attach($user, [
                'role' => UserTypeEnum::Admin->value,
            ]);
        });
    }

    public function user(User $user): static
    {
        return $this->afterCreating(function (App $app) use ($user) {
            $app->users()->attach($user, [
                'role' => UserTypeEnum::User->value,
            ]);
        });
    }
}
