<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('document_statuses')->insert([
            ['code'=>'RECEIVED','name'=>'Received'],
            ['code'=>'LOGGED','name'=>'Logged in Registry'],
            ['code'=>'FORWARDED','name'=>'Forwarded'],
            ['code'=>'ACKNOWLEDGED','name'=>'Acknowledged'],
            ['code'=>'FOR_REPLY','name'=>'For Reply'],
            ['code'=>'REPLY_PREPARED','name'=>'Reply Prepared'],
            ['code'=>'TRANSMITTED','name'=>'Transmitted'],
            ['code'=>'ARCHIVED','name'=>'Archived'],
        ]);
    }
}
