<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('student_id')->unique()->nullable(); // e.g. IT22100123
            $table->string('faculty')->nullable();              // e.g. Faculty of Computing
            $table->tinyInteger('year')->nullable();            // 1, 2, 3, 4
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();               // profile photo path
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
