<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'code' => 'ADM',
                'name' => 'CCC Administrative Office',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
