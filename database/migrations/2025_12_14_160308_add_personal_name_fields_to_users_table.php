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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('id');
            $table->string('first_name')->nullable()->after('last_name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('name_extension', 20)->nullable()->after('middle_name');
            $table->string('post_nominals')->nullable()->after('name_extension');

            // keep legacy name for backward compatibility
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'first_name',
                'middle_name',
                'name_extension',
                'post_nominals',
            ]);
        });
    }
};
