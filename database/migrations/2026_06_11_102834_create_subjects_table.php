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
        Schema::create('subject_master', function (Blueprint $table) {
            $table->id('subject_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('internal_full_marks', 10, 2)->default(0);
            $table->decimal('internal_pass_marks', 10, 2)->default(0);
            $table->decimal('theory_full_marks', 10, 2)->default(0);
            $table->decimal('theory_pass_marks', 10, 2)->default(0);
            $table->decimal('practical_full_marks', 10, 2)->default(0);
            $table->decimal('practical_pass_marks', 10, 2)->default(0);

            $table->foreignId('programme_id')->nullable()->constrained('programme_master', 'programme_id')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('course_master', 'course_id')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_master');
    }
};
