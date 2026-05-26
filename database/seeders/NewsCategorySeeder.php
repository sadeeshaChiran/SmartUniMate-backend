<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news_categories')->insert([
            ['id' => 1, 'name' => 'Academic', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Examinations', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Events', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Administration', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Sports', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Research', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
