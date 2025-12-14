<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Memo vs approval
            $table->boolean('is_memo')->default(false);
            $table->boolean('is_for_approval')->default(false);

            // Audience for memos
            $table->enum('audience_type', ['all', 'department', 'users'])->nullable()->after('is_memo');
            $table->json('audience_users')->nullable()->after('audience_type'); // [1,2,3]

            // Approval routing (manual chain)
            $table->unsignedBigInteger('current_approver_id')->nullable()->after('is_for_approval');
            $table->json('next_approver_ids')->nullable()->after('current_approver_id'); // [9,10]

            // Acknowledge tracking
            $table->json('acknowledged_by')->nullable()->after('next_approver_ids');

            // keep previous multi-level fields if present (no-op if already)
            // if (!Schema::hasColumn('documents', 'approver_level')) {
            //     $table->integer('approver_level')->default(1)->after('acknowledged_by');
            // }
            // if (!Schema::hasColumn('documents', 'approved_level_1_by')) {
            //     $table->unsignedBigInteger('approved_level_1_by')->nullable()->after('approver_level');
            //     $table->unsignedBigInteger('approved_level_2_by')->nullable()->after('approved_level_1_by');
            //     $table->unsignedBigInteger('approved_level_3_by')->nullable()->after('approved_level_2_by');
            // }
            // if (!Schema::hasColumn('documents', 'status')) {
            //     $table->enum('status', ['pending','in_review','rejected','completed','information'])->default('pending')->after('approved_level_3_by');
            // }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'is_memo',
                'is_for_approval',
                'audience_type',
                'audience_users',
                'current_approver_id',
                'next_approver_ids',
                'acknowledged_by',
            ]);
            // // be careful about dropping other columns — keep them if used elsewhere
            // if (Schema::hasColumn('documents', 'approver_level')) {
            //     $table->dropColumn('approver_level');
            // }
            // if (Schema::hasColumn('documents', 'approved_level_1_by')) {
            //     $table->dropColumn([
            //         'approved_level_1_by',
            //         'approved_level_2_by',
            //         'approved_level_3_by',
            //     ]);
            // }
            // if (Schema::hasColumn('documents', 'status')) {
            //     // don't drop status if applications depend on it — uncomment if safe
            //     // $table->dropColumn('status');
            // }
        });
    }
};
