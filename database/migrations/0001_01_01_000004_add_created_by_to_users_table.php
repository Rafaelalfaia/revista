<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','created_by_id')) {
                $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','created_by_id')) {
                $table->dropConstrainedForeignId('created_by_id');
            }
        });
    }
};
