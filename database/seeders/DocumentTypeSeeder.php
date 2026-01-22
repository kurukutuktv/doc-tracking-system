<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('document_types')->insert([
            ['name'=>'Letter'],
            ['name'=>'Memorandum'],
            ['name'=>'Endorsement'],
            ['name'=>'Request'],
        ]);
    }
}
