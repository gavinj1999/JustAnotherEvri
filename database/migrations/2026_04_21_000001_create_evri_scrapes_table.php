<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evri_scrapes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('round')->default('');
            $table->decimal('earnings', 8, 2)->nullable();
            $table->unsignedInteger('parcel_count')->nullable();
            $table->decimal('execution_time', 8, 2)->nullable();
            $table->string('status');
            $table->text('raw_output')->nullable();
            $table->string('execution_id')->unique();
            $table->timestamps();

            // Prevent duplicate scrape for the same date + round
            $table->unique(['date', 'round']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evri_scrapes');
    }
};
