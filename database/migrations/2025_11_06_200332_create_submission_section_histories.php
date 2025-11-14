<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
   Schema::create('submission_section_histories', function (Blueprint $t) {
        $t->id();

        $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
        $t->foreignId('section_id')->constrained('submission_sections')->cascadeOnDelete();

        $t->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete(); // mantém histórico mesmo se o usuário for removido
        $t->foreignId('origin_comment_id')->nullable()->constrained('submission_comments')->nullOnDelete(); // opcional

        $t->longText('old_content')->nullable();
        $t->longText('new_content')->nullable();

        $t->timestamps();

        $t->index(['submission_id','section_id','created_at']);
        $t->index('edited_by');
    });

  }
  public function down(): void
  {
    Schema::dropIfExists('submission_section_histories');
  }
};
