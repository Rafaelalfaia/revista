<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('review_assignments')) {
            Schema::create('review_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->string('status', 30)->default('atribuida')->index();
                $table->timestamp('assigned_at')->nullable();
                $table->timestamps();
                $table->unique(['submission_id','reviewer_id']);
            });
        }

        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assignment_id')->nullable()->constrained('review_assignments')->nullOnDelete();
                $table->string('status', 30)->default('atribuida')->index();
                $table->text('recommendation')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
                $table->index(['submission_id','reviewer_id']);
            });
        }

        if (!Schema::hasTable('submission_comments')) {
            Schema::create('submission_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
                $table->foreignId('review_id')->nullable()->constrained('reviews')->nullOnDelete();
                $table->foreignId('section_id')->nullable()->constrained('submission_sections')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('level', 20)->default('note')->index();   // ex.: must_fix, should_fix, note
                $table->string('status', 20)->default('open')->index();  // ex.: open, resolved
                $table->text('body');
                $table->timestamps();
                $table->index(['submission_id','status','level']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_comments');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('review_assignments');
    }
};
