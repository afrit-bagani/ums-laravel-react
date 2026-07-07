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
        Schema::create('student_paper_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->cascadeOnDelete();
            $table->foreignId('programme_id')->constrained('programme_master', 'programme_id')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course_master', 'course_id')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batch_master', 'batch_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_paper_selections');
    }
};
