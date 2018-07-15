<?php

use Illuminate\Database\Seeder;

class CorpsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Corps::class, 50)->create();
    }
}
