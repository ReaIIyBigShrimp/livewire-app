<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Record;
use Faker\Factory as Faker;

class RecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 20; $i++) {
            Record::create(['name' => $faker->name, 'email' => $faker->unique()->safeEmail, 'phone' => '07' . $faker->unique()->numerify('#########'),]);
        }
    }
}
