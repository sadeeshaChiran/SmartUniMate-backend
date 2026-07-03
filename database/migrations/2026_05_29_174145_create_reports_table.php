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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('reported_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('reported_post_id')->nullable()->constrained('communities')->nullOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->string('status')->default('pending');
            $table->string('action_taken')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
