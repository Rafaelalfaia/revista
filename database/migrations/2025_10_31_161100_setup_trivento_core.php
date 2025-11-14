<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('abstract')->nullable();
            $table->string('language', 10)->default('pt-BR');
            $table->string('tipo_trabalho', 50)->index();
            $table->json('keywords')->nullable();
            $table->json('meta')->nullable();
            $table->string('doi')->nullable()->unique();
            $table->string('status', 30)->default('rascunho')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('triaged_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('numbering_config')->nullable();
            $table->json('pagination_config')->nullable();
            $table->timestamps();
            $table->index(['tipo_trabalho','status']);
        });

        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('role', 32)->index();
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum', 64)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_current')->default(true);
            $table->timestamps();
            $table->index(['submission_id','role']);
            $table->unique(['submission_id','role','version']);
        });

        Schema::create('submission_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('submission_sections')->nullOnDelete();
            $table->unsignedInteger('position');
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('label', 50)->nullable();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('numbering', 50)->nullable();
            $table->boolean('show_number')->default(true);
            $table->boolean('show_in_toc')->default(true);
            $table->timestamps();
            $table->unique(['submission_id','parent_id','position']);
        });

        Schema::create('submission_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('submission_sections')->nullOnDelete();
            $table->string('type', 20);
            $table->string('label', 50)->nullable();
            $table->string('numbering', 50)->nullable();
            $table->text('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('source')->nullable();
            $table->string('disk', 32)->default('public');
            $table->string('file_path');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('order')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['submission_id','section_id']);
        });

        Schema::create('submission_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(1);
            $table->string('citekey')->nullable();
            $table->text('raw');
            $table->json('parsed_json')->nullable();
            $table->string('doi')->nullable();
            $table->string('url')->nullable();
            $table->date('accessed_at')->nullable();
            $table->timestamps();
            $table->index(['submission_id','order']);
            $table->unique(['submission_id','doi']);
        });

        Schema::create('submission_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->json('data')->nullable();
            $table->timestamps();
            $table->unique('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_metadata');
        Schema::dropIfExists('submission_references');
        Schema::dropIfExists('submission_assets');
        Schema::dropIfExists('submission_sections');
        Schema::dropIfExists('submission_files');
        Schema::dropIfExists('submissions');
    }
};
