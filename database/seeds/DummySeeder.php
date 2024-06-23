<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* DEPARTMENT */
        DB::table('departments')->insert([
            'department' => 'TELKOM',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('departments')->insert([
            'department' => 'SCADA',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('departments')->insert([
            'department' => 'PROSIS',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('departments')->insert([
            'department' => 'UPT',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('departments')->insert([
            'department' => 'DISPATCHER',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);

        /* LOCATION */
        DB::table('locations')->insert([
            'location' => 'GI KUDUS 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI UNGARAN 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI SEMARANG 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI SALATIGA 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI DEMAK 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI JEPARA 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI BOYOLALI 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI BATANG 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI KENDAL 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GI WLERI 150 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('locations')->insert([
            'location' => 'GITET UNGARAN 500 KV',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'active' => 1,
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);

        /* GENERAL CODES */
        //WO TYPE
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'WO_TYPE',
            'reff1' => 'LAPORAN GANGGUAN',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'WO_TYPE',
            'reff1' => 'PEKERJAAN',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);

        //JOB CATEGORY
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'JOB_CATEGORY',
            'reff1' => 'PERBAIKAN',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'JOB_CATEGORY',
            'reff1' => 'IMPROVEMENT',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'JOB_CATEGORY',
            'reff1' => 'PEMBANGUNAN',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);

        //DISTURBANCE CATEGORY
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'DISTURBANCE_CATEGORY',
            'reff1' => 'TP OFF',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'DISTURBANCE_CATEGORY',
            'reff1' => 'TP LINK DOWN',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('general_codes')->insert([
            'section' => 'SPONGE',
            'label' => 'DISTURBANCE_CATEGORY',
            'reff1' => 'TP ERROR',
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);

        /* DEVICES */
        DB::table('devices')->insert([
            'device_name' => 'RESISTOR',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'MODEM',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'ROUTER',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'REPEATER RADIO',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'MUX',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'RADIO VHF',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'FIBER OPTIC',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'PLC',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
        DB::table('devices')->insert([
            'device_name' => 'UC PHONE',
            'location_id' => 2,
            'start_effective' => Carbon::create('2024', '01', '01'),
            'created_by' => 1,
            'created_at' => Carbon::create('2024', '01', '01'),
            'updated_by' => 1,
            'updated_at' => Carbon::create('2024', '01', '01'),
        ]);
    }
}
