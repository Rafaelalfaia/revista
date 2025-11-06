<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('review_assignments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $t->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();

            $t->string('status')->default('pending');
            $t->timestamp('assigned_at')->nullable();
            $t->timestamp('due_at')->nullable();
            $t->timestamps();

            $t->unique(['submission_id','reviewer_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('review_assignments'); }
};
