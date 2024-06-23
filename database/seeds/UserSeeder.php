<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'department_id' => 1,
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123'),
            'nik' => '123456',
            'gender' => 'PRIA',
            'active' => 1,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
    }
}
