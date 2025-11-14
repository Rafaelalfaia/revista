<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('submission_comments', function (Blueprint $t) {
      if (!Schema::hasColumn('submission_comments','audience')) {

        $t->string('audience', 12)->default('author')->index();
      }
      if (!Schema::hasColumn('submission_comments','body')) {

        $t->text('body')->nullable();
      }
      if (!Schema::hasColumn('submission_comments','status')) {

        $t->string('status', 16)->default('open')->index();
      }
      if (!Schema::hasColumn('submission_comments','level')) {

        $t->string('level', 16)->default('should_fix')->index();
      }
      if (!Schema::hasColumn('submission_comments','section_id')) {
        $t->foreignId('section_id')->nullable()->constrained('submission_sections')->nullOnDelete();
      }
    });
  }

  public function down(): void
  {
    Schema::table('submission_comments', function (Blueprint $t) {
      if (Schema::hasColumn('submission_comments','audience')) $t->dropColumn('audience');
      if (Schema::hasColumn('submission_comments','body'))     $t->dropColumn('body');

    });
  }
};
