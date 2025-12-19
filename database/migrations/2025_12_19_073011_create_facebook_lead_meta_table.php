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
        Schema::create('facebook_lead_meta', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // Core relationship
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();

            // Facebook identifiers
            $table->string('leadgen_id')->unique();
            $table->string('form_id');
            $table->string('page_id');

            // Optional but very useful
            $table->json('raw_payload')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();

            // Helpful indexes
            $table->index(['page_id', 'form_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_lead_meta');
    }
};
