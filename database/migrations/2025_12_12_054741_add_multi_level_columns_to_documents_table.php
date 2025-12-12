<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('approver_level')->default(1);

            $table->unsignedBigInteger('approved_level_1_by')->nullable();
            $table->unsignedBigInteger('approved_level_2_by')->nullable();
            $table->unsignedBigInteger('approved_level_3_by')->nullable();

            $table->enum('status', ['pending', 'in_review', 'rejected', 'completed'])
                ->default('pending');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'approver_level',
                'approved_level_1_by',
                'approved_level_2_by',
                'approved_level_3_by',
                'status',
            ]);
        });
    }
};
