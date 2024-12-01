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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('app_id')->references('id')->on('apps');
            $table->foreignUuid('category_id')->references('id')->on('categories');
            $table->string('title');
            $table->mediumText('description')->nullable();
            $table->json('content')->nullable();
            $table->mediumText('ai_summary')->nullable();
            $table->json('sample_questions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
