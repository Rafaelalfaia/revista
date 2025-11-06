<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('templates', function (Blueprint $t) {
      $t->id();
      $t->string('key')->unique();
      $t->string('title');
      $t->jsonb('json_spec'); // layout/estilos/toC/cover etc.
      $t->string('description')->nullable();
      $t->boolean('is_active')->default(true);
      $t->unsignedSmallInteger('version')->default(1);
      $t->timestamps();
    });

    Schema::create('submissions', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->string('title');
      $t->string('slug')->unique();
      $t->text('abstract')->nullable();
      $t->string('language', 10)->default('pt-BR');
      $t->jsonb('keywords')->nullable();
      $t->jsonb('meta')->nullable();

      // Apenas os 6 tipos que você vai usar
      $t->enum('tipo_trabalho', [
        'artigo_original',
        'comunicacao_breve',
        'revisao_narrativa',
        'revisao_sistematica',
        'relato_caso',
        'relato_tecnico'
      ])->default('artigo_original');

      $t->enum('status', [
        'rascunho','submetido','em_triagem','em_revisao',
        'revisao_solicitada','aceito','rejeitado','publicado'
      ])->default('rascunho');

      // Novos campos
      $t->string('doi')->nullable()->unique();
      $t->jsonb('numbering_config')->nullable();   // ver exemplo abaixo
      $t->jsonb('pagination_config')->nullable();  // ver exemplo abaixo

      $t->timestamp('submitted_at')->nullable();
      $t->timestamp('triaged_at')->nullable();
      $t->timestamp('accepted_at')->nullable();
      $t->timestamp('published_at')->nullable();
      $t->timestamps();

      $t->index(['tipo_trabalho','status']);
    });

    Schema::create('submission_files', function (Blueprint $t) {
      $t->id();
      $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
      $t->string('role', 40); // manuscript, cover_letter, fig, table, supplement, prisma_checklist...
      $t->string('disk')->default('private');
      $t->string('path');
      $t->string('original_name');
      $t->string('mime', 120)->nullable();
      $t->unsignedBigInteger('size')->nullable();
      $t->string('hash', 64)->nullable(); // sha256 p/ deduplicação
      $t->unsignedSmallInteger('version')->default(1);
      $t->timestamps();
      $t->index(['submission_id','role']);
    });

    Schema::create('submission_sections', function (Blueprint $t) {
      $t->id();
      $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
      $t->foreignId('parent_id')->nullable()
        ->constrained('submission_sections')->cascadeOnDelete();

      $t->unsignedInteger('position')->default(1); // ordem entre irmãos
      $t->unsignedTinyInteger('level')->default(1); // 1..N
      $t->string('title');
      $t->text('content')->nullable(); // markdown/html
      $t->string('numbering')->nullable(); // override manual (ex.: "A", "S1")
      $t->boolean('show_number')->default(true); // ex.: Referências/Agradecimentos = false
      $t->boolean('show_in_toc')->default(true);

      $t->timestamps();
      $t->index(['submission_id','parent_id','position']);
      $t->unique(['submission_id','parent_id','position']); // garante posição única por nível
    });

    Schema::create('submission_assets', function (Blueprint $t) {
      $t->id();
      $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
      $t->foreignId('section_id')->nullable()
        ->constrained('submission_sections')->nullOnDelete();

      $t->enum('type', ['figure','table','attachment']);
      $t->string('disk')->default('private');
      $t->string('file_path');
      $t->string('mime', 120)->nullable();
      $t->unsignedBigInteger('size')->nullable();

      $t->string('label')->nullable();     // "Figura", "Tabela", "Quadro"...
      $t->string('numbering')->nullable(); // "1", "2", "S1" (suplementar), etc.
      $t->string('caption')->nullable();
      $t->string('alt_text')->nullable();  // acessibilidade
      $t->string('source')->nullable();

      $t->unsignedInteger('order')->default(1);
      $t->timestamps();
      $t->index(['submission_id','section_id','type']);
    });

    Schema::create('submission_references', function (Blueprint $t) {
      $t->id();
      $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
      $t->unsignedInteger('order')->default(1);
      $t->string('citekey', 120)->nullable(); // ex.: SOBRENOME2024
      $t->text('raw');                  // referência em texto
      $t->jsonb('parsed_json')->nullable(); // CSL-JSON/BibTeX parseado
      $t->string('doi')->nullable();
      $t->string('url')->nullable();
      $t->date('accessed_at')->nullable();
      $t->timestamps();
      $t->unique(['submission_id','doi']);
      $t->index(['submission_id','order']);
    });

    Schema::create('submission_metadata', function (Blueprint $t) {
      $t->id();
      $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
      $t->jsonb('data'); // afiliações normalizadas, CRediT, ethics, funding etc.
      $t->timestamps();
      $t->unique('submission_id');
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
    Schema::dropIfExists('templates');
  }
};
