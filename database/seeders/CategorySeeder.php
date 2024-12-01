<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Article',
                'description' => 'Teach your users how...',
            ],
            [
                'name' => 'News',
                'description' => 'Update your users on..',
            ],
            [
                'name' => 'Changelog',
                'description' => 'Whats happening?',
            ],
        ];

        collect($categories)->each(function ($category) {
            Category::updateOrCreate([
                'name' => $category['name'],
            ], [
                'description' => $category['description'],
            ]);
        });
    }
}
