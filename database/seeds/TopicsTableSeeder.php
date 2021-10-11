<?php

use Illuminate\Database\Seeder;
Use App\Topic;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Let's truncate our existing records to start from scratch.
        Topic::truncate();

        $faker = \Faker\Factory::create();

        // And now, let's create a few articles in our database:
        for ($i = 0; $i < 50; $i++) {
            Topic::create([
                'name' => $faker->sentence,
                'message' => $faker->paragraph,
            ]);
        }
    }
}
