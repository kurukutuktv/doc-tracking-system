<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('approval_hierarchies', function (Blueprint $table) {
            $table->id();

            // Department this hierarchy belongs to
            $table->foreignId('department_id')
                ->constrained()
                ->cascadeOnDelete();

            // Approval level (1 = first approver, 2 = second, etc.)
            $table->unsignedTinyInteger('level');

            // Role required at this level
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();
            $table->timestamps();

            // Prevent duplicate levels per department
            $table->unique(['department_id', 'level']);

            // Optional safety: same role cannot repeat per department
            $table->unique(['department_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_hierarchies');
    }
};