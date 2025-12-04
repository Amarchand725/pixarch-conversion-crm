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
        Schema::create('capture_form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_capture_id')->nullable()->constrained('lead_captures')->cascadeOnUpdate()->nullOnDelete();
            $table->string('label');
            $table->string('name');
            $table->string('type');
            $table->string('placeholder')->nullable();
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();  // for select/radio/checkbox
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capture_form_fields');
    }
};
