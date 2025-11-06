<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cpf')) {
                // nullable + índice único
                $table->string('cpf', 11)->nullable();
            }
        });

        DB::statement("DO $$
        BEGIN
          IF NOT EXISTS (
            SELECT 1 FROM pg_indexes WHERE tablename = 'users' AND indexname = 'users_cpf_unique'
          ) THEN
            CREATE UNIQUE INDEX users_cpf_unique ON users (cpf);
          END IF;
        END$$;");

        DB::statement('ALTER TABLE users ALTER COLUMN email DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement("DO $$
        BEGIN
          IF EXISTS (
            SELECT 1 FROM pg_indexes WHERE tablename = 'users' AND indexname = 'users_cpf_unique'
          ) THEN
            DROP INDEX users_cpf_unique;
          END IF;
        END$$;");

        // Remove coluna se existir
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'cpf')) {
                $table->dropColumn('cpf');
            }
        });

    }
};
