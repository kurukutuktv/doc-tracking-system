<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Classification
            $table->enum('direction', ['INCOMING', 'OUTGOING']);
            $table->foreignId('document_type_id')->constrained();
            $table->enum('priority_level', ['LOW','NORMAL','HIGH','URGENT'])
                  ->default('NORMAL');

            // Registry
            $table->string('control_number')->unique()->nullable();
            $table->date('registry_date')->nullable();
            $table->date('received_date')->nullable();
            $table->date('sent_date')->nullable();

            // Content
            $table->string('title');
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path');

            // Routing
            $table->foreignId('sender_id')->nullable()
                  ->constrained('departments');
            $table->string('sender_name')->nullable();
            $table->foreignId('recipient_id')->nullable()
                  ->constrained('departments');
            $table->foreignId('current_office_id')
                  ->constrained('departments');

            // Workflow
            $table->foreignId('status_id')
                  ->constrained('document_statuses');
            $table->foreignId('parent_document_id')->nullable()
                  ->constrained('documents');

            // Transmission (Outgoing only)
            $table->foreignId('transmission_mode_id')->nullable()
                  ->constrained();

            // Audit
            $table->foreignId('created_by')
                  ->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
