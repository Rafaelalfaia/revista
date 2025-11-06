<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('category_submission', function (Blueprint $t) {
      $t->id();
      $t->foreignId('category_id')->constrained()->cascadeOnDelete();
      $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
      $t->boolean('is_primary')->default(false);
      $t->timestamps();
      $t->unique(['category_id','submission_id']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('category_submission');
  }
};
