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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->index();
            $table->unsignedBigInteger('lead_id')->nullable()->constrained('leads')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('status_id')->nullable()->constrained('statuses')->cascadeOnUpdate()->nullOnDelete();
            $table->string('time_zone');
            $table->timestamp('start_date_time')->nullable();
            $table->timestamp('end_date_time')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
