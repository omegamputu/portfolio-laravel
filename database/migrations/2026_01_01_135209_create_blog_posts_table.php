<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            //$table->text('excerpt')->nullable();
            $table->longText('content');

            $table->string('cover_image')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedSmallInteger('reading_time')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 160)->nullable();

            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
