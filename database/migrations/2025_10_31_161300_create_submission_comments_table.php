<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submission_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('submission_sections')->nullOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('submission_comments')->nullOnDelete();
            $table->string('level', 30)->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->text('quote')->nullable();
            $table->text('note');
            $table->foreignId('resolver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('verified_by_reviewer_at')->nullable();
            $table->timestamps();
            $table->index(['submission_id','section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_comments');
    }
};
