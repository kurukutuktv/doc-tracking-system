<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransmissionModeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transmission_modes')->insert([
            ['name'=>'Courier'],
            ['name'=>'Email'],
            ['name'=>'Hand Carry'],
        ]);
    }
}
