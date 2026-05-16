<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('subject');         // e.g. Software Engineering
            $table->string('lecturer');        // e.g. Dr. Silva
            $table->string('room');            // e.g. Lab 03
            $table->string('day');             // Monday, Tuesday, etc.
            $table->time('start_time');        // e.g. 08:00
            $table->time('end_time');          // e.g. 10:00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
