<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            
            $table->string('opportunity_name')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('pipeline')->nullable();
            $table->string('stage')->nullable();

            $table->decimal('lead_value', 15, 2)->nullable();
            $table->string('source')->nullable();
            $table->string('assigned')->nullable();

            $table->dateTime('created_on')->nullable();
            $table->dateTime('updated_on')->nullable();

            $table->string('lost_reason_id')->nullable();
            $table->string('lost_reason_name')->nullable();

            $table->text('followers')->nullable();
            $table->longText('notes')->nullable();
            $table->string('tags')->nullable();

            $table->integer('engagement_score')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('last_updates_on')->nullable();

            $table->string('opportunity_id')->nullable();
            $table->string('contact_id')->nullable();
            $table->string('pipeline_stage_id')->nullable();
            $table->string('pipeline_id')->nullable();

            $table->integer('days_since_last_stage_change')->nullable();
            $table->integer('days_since_last_status_change')->nullable();
            $table->integer('days_since_last_updated')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
