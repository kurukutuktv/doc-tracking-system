<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // migration
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('target_department_id')
                ->nullable()
                ->after('created_by')
                ->constrained('departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
