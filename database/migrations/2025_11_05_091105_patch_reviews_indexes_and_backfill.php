<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Índices úteis para a listagem do Revisor
        DB::statement("CREATE INDEX IF NOT EXISTS reviews_reviewer_status_index ON reviews (reviewer_id, status)");
        DB::statement("CREATE INDEX IF NOT EXISTS reviews_submission_id_index   ON reviews (submission_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS reviews_assigned_at_index     ON reviews (assigned_at)");

        DB::statement("UPDATE reviews SET assigned_at = created_at WHERE assigned_at IS NULL");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS reviews_reviewer_status_index");
        DB::statement("DROP INDEX IF EXISTS reviews_submission_id_index");
        DB::statement("DROP INDEX IF EXISTS reviews_assigned_at_index");
    }
};
