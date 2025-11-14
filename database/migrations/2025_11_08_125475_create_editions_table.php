<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();

            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('profile_photo_disk', 64)->default('public');
            $table->string('cover_photo_path', 2048)->nullable();
            $table->string('cover_photo_disk', 64)->default('public');

            $table->date('release_date')->nullable();
            $table->timestampTz('published_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
