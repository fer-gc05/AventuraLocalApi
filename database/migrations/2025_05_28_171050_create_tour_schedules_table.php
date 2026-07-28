<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('guide_id')->constrained('guide_profiles')->onDelete('cascade');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->unsignedInteger('max_spots');
            $table->unsignedInteger('booked_spots')->default(0);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->enum('status', ['available', 'closed', 'cancelled'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tour_id', 'start_datetime']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_schedules');
    }
};
