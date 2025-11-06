<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
                $table->string('status', 40)->default('atribuida');
                $table->timestamp('requested_corrections_at')->nullable();
                $table->timestamp('submitted_opinion_at')->nullable();
                $table->string('recommendation', 40)->nullable();
                $table->timestamps();

                $table->unique(['submission_id','reviewer_id']);
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
