<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jobs')->insert([
            'wo_category' => 'LAPORAN GANGGUAN',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'SURVEY',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'PEMASANGAN',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'SUPERVISI',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'COMMISIONING',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'RESETTING',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'INVESTIGASI',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'PERBAIKAN',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('jobs')->insert([
            'wo_category' => 'PEKERJAAN',
            'job_category' => 'DISMANTLING',
            'job' => '',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
    }
}
