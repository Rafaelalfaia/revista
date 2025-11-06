<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        $table = 'submission_comments';

        Schema::table($table, function (Blueprint $t) use ($table) {
            if (!Schema::hasColumn($table, 'section_id')) {
                $t->foreignId('section_id')->nullable()
                  ->constrained('submission_sections')->nullOnDelete();
            }
            if (!Schema::hasColumn($table, 'parent_id')) {
                $t->unsignedBigInteger('parent_id')->nullable();
                $t->foreign('parent_id')->references('id')->on($table)->nullOnDelete();
            }
            if (!Schema::hasColumn($table, 'author_role_at_creation')) {
                $t->string('author_role_at_creation', 32)->nullable()->index();
            }
            if (!Schema::hasColumn($table, 'level')) {
                // usamos string para evitar complicações de enum no Postgres
                $t->string('level', 16)->default('should_fix')->index();
            }
            if (!Schema::hasColumn($table, 'excerpt')) {
                $t->text('excerpt')->nullable();
            }
            if (!Schema::hasColumn($table, 'suggested_text')) {
                $t->text('suggested_text')->nullable();
            }
            if (!Schema::hasColumn($table, 'status')) {
                $t->string('status', 16)->default('open')->index(); // open|accepted|rejected|applied...
            }
            if (!Schema::hasColumn($table, 'resolver_id')) {
                $t->foreignId('resolver_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn($table, 'resolved_at')) {
                $t->timestamp('resolved_at')->nullable()->index();
            }
            if (!Schema::hasColumn($table, 'resolved_by_author_at')) {
                $t->timestamp('resolved_by_author_at')->nullable()->index();
            }
            if (!Schema::hasColumn($table, 'verified_by_reviewer_at')) {
                $t->timestamp('verified_by_reviewer_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {

    }
};
