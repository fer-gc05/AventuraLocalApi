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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('guide_id')->constrained('guide_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('destination_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meeting_point')->nullable();
            $table->decimal('meeting_latitude', 10, 8)->nullable();
            $table->decimal('meeting_longitude', 11, 8)->nullable();
            $table->decimal('price_per_person', 10, 2);
            $table->string('currency')->default('COP');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('max_participants');
            $table->unsignedInteger('min_participants')->default(1);
            $table->json('languages')->nullable();
            $table->json('includes')->nullable();
            $table->json('excludes')->nullable();
            $table->json('what_to_bring')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->enum('status', ['draft', 'published', 'paused', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('is_active');
            $table->index(['meeting_latitude', 'meeting_longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
