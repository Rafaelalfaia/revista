<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submission_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('submission_comments', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->after('section_id');
            }

            if (!Schema::hasColumn('submission_comments', 'audience')) {
                $table->string('audience', 12)->default('both')->after('user_id'); // author|reviewer|both
            }

            if (!Schema::hasColumn('submission_comments', 'level')) {
                $table->string('level', 20)->default('should_fix')->after('audience'); // must_fix|should_fix|nit
            }

            if (!Schema::hasColumn('submission_comments', 'status')) {
                $table->string('status', 20)->default('open')->after('level'); // open|applied|accepted|rejected
            }

            if (!Schema::hasColumn('submission_comments', 'resolver_id')) {
                $table->foreignId('resolver_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->after('status');
            }

            if (!Schema::hasColumn('submission_comments', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('resolver_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('submission_comments', function (Blueprint $table) {
            if (Schema::hasColumn('submission_comments','resolved_at')) $table->dropColumn('resolved_at');
            if (Schema::hasColumn('submission_comments','resolver_id'))  $table->dropConstrainedForeignId('resolver_id');
            if (Schema::hasColumn('submission_comments','status'))       $table->dropColumn('status');
            if (Schema::hasColumn('submission_comments','level'))        $table->dropColumn('level');
            if (Schema::hasColumn('submission_comments','audience'))     $table->dropColumn('audience');
            if (Schema::hasColumn('submission_comments','user_id'))      $table->dropConstrainedForeignId('user_id');
        });
    }
};
