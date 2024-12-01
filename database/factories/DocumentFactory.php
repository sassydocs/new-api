<?php

namespace Database\Factories;

use App\Http\Enum\V1\ContentTypeEnum;
use App\Http\Enum\V1\UserTypeEnum;
use App\Models\App;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
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
            'title' => fake()->words(5, true),
            'description' => fake()->paragraph(),
            'category_id' => Category::factory()->create(),
        ];
    }

    public function content(int $size = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $this->generateRandomContent($size),
        ]);
    }

    protected function generateRandomContent(int $size): array
    {
        $response = [];

        for ($i = 0; $i < $size; $i++) {
            $type = fake()->randomElement(ContentTypeEnum::cases());

            switch ($type) {
                case ContentTypeEnum::Paragraph:
                    $data = [
                        'text' => fake()->paragraph(),
                    ];
                    break;
                case ContentTypeEnum::Heading:
                    $data = [
                        'text' => fake()->words(10, true),
                        'level' => fake()->randomElement([1, 2, 3, 4, 5, 6]),
                    ];
                    break;
                case ContentTypeEnum::LinkEmbed:
                    $data = [
                        'link' => fake()->url(),
                        'meta' => [
                            'title' => fake()->words(10, true),
                            'site_name' => fake()->company,
                            'description' => fake()->paragraph(),
                            'image' => [
                                'url' => fake()->url(),
                            ]
                        ]
                    ];
                    break;
                case ContentTypeEnum::Image:
                    $data = [
                        'data' => [
                            'caption' => fake()->words(10, true),
                            'withBorder' => fake()->boolean,
                            'withBackground' => fake()->boolean,
                            'stretched' => fake()->boolean,
                            'file' => [
                                'url' => fake()->url(),
                            ]
                        ]
                    ];
                    break;
                case ContentTypeEnum::Quote:
                    $data = [
                        'text' => fake()->words(10, true),
                        'caption' => fake()->name,
                        'alignment' => fake()->randomElement(['left', 'center'])
                    ];
                    break;
            }

            $response[] = [
                'type' => $type,
                'data' => $data,
            ];
        }

        return $response;
    }
}
