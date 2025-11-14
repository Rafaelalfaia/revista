<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('reviews', 'submitted_opinion_at')) {
            Schema::table('reviews', function (Blueprint $table) {
                // timestamp com fuso/data-hora; pode ser null até o parecer ser enviado
                $table->timestamp('submitted_opinion_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('reviews', 'submitted_opinion_at')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('submitted_opinion_at');
            });
        }
    }
};
