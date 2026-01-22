<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transmission_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Courier, Email, Hand Carry
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transmission_modes');
    }
};
