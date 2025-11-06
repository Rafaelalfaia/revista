<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('submission_comments', function (Blueprint $t){
            $t->id();
            $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $t->foreignId('section_id')->nullable()->constrained('submission_sections')->nullOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->text('quote');   // texto destacado
            $t->text('note');    // observação
            $t->string('page_mode', 10)->nullable(); // 'single' | 'dual' (apenas referência)
            $t->timestamps();

            $t->index(['submission_id','section_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('submission_comments');
    }
};
