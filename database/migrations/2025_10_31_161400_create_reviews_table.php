<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('atribuida')->index();
            $table->string('recommendation', 50)->nullable();
            $table->timestamp('assigned_at')->nullable()->index();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['submission_id','reviewer_id']);
            $table->index('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
