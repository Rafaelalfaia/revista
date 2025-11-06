<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $t) {
            if (!Schema::hasColumn('reviews', 'assigned_at')) {
                $t->timestamp('assigned_at')->nullable()->index();
            }
            if (!Schema::hasColumn('reviews', 'due_at')) {
                $t->timestamp('due_at')->nullable()->index();
            }
            $t->index(['reviewer_id', 'status']);
        });

        if (Schema::hasTable('review_assignments')) {
            DB::statement("
                INSERT INTO reviews (submission_id, reviewer_id, status, assigned_at, due_at, created_at, updated_at)
                SELECT ra.submission_id,
                       ra.reviewer_id,
                       CASE WHEN ra.status = 'pending' THEN 'atribuida' ELSE ra.status END,
                       ra.assigned_at,
                       ra.due_at,
                       ra.created_at,
                       ra.updated_at
                FROM review_assignments ra
                ON CONFLICT (submission_id, reviewer_id) DO NOTHING
            ");
        }
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $t) {
            if (Schema::hasColumn('reviews','assigned_at')) $t->dropColumn('assigned_at');
            if (Schema::hasColumn('reviews','due_at'))      $t->dropColumn('due_at');
        });
    }
};
