<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('edition_submission', function (Blueprint $table) {
            $table->id();

            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();

            $table->unsignedInteger('position')->default(1);
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['edition_id','submission_id']);
            $table->unique(['edition_id','position']);
            $table->index(['edition_id','created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edition_submission');
    }
};
