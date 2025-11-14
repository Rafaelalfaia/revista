<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (!Schema::hasColumn('submission_assets','disk')) {
      Schema::table('submission_assets', function (Blueprint $t) {
        $t->string('disk', 20)->default('public');
      });
    }
  }
  public function down(): void {
    if (Schema::hasColumn('submission_assets','disk')) {
      Schema::table('submission_assets', fn (Blueprint $t) => $t->dropColumn('disk'));
    }
  }
};
