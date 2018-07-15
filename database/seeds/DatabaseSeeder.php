<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_name' => 'zhongsiran',
            'user_real_name' => '钟思燃',
            'password' => md5('661668'),
            'user_group' => 'sys_admin',
            'user_aic_division' => 'SL',
            'slaic_openid' => 'oETUlv0ZFWJhAM_YdT5uOj1QHgJA',
            'active_status' => true,
        ]);

        $this->call(CorpsTableSeeder::class);
    }
}
