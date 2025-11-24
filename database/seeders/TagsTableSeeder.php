<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarTag;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            'NFL', 'IPL', 'NBA', 'Premier League', 'La Liga',
            'Big Screens', 'Happy Hour', 'Outdoor Seating', 'Live Music',
            'Family Friendly', 'Sports Bar', 'Craft Beer', 'Cocktails'
        ];

        foreach ($tags as $name) {
            BarTag::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'slug' => Str::slug($name)]
            );
        }
    }
}
