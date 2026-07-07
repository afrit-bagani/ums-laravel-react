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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // --- PERSONAL INFORMATION ---
            $table->string('registration_number')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('full_name', 50);
            $table->string('father_name', 50);
            $table->string('mother_name', 50);
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('dob');
            $table->string('abc_id', 12)->nullable()->unique();
            $table->string('aadhaar_no', 12)->nullable()->unique();
            $table->string('nationality', 50);
            $table->string('mobile_no', 10)->unique();
            $table->string('email')->unique();
            $table->enum('religion', ['Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist', 'Jain']);
            $table->enum('caste', ['General', 'SC', 'ST', 'OBC', 'EWS']);
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed']);
            $table->decimal('annual_family_income', 15, 2)->nullable();
            $table->string('parent_mobile_no', 10)->nullable();

            $table->boolean('is_blind')->default(false);
            $table->boolean('is_bpl')->default(false); // Below Poverty Line
            $table->boolean('is_minority')->default(false);
            $table->boolean('is_ph')->default(false); // Physically Handicapped

            // --- ADDRESS INFORMATION ---
            $table->string('present_address', 255);
            $table->string('present_city', 50);
            $table->string('present_country', 50);
            $table->string('present_state', 50);
            $table->string('present_district', 50);
            $table->string('present_pincode', 6);

            $table->string('permanent_address', 255);
            $table->string('permanent_city', 50);
            $table->string('permanent_country', 50);
            $table->string('permanent_state', 50);
            $table->string('permanent_district', 50);
            $table->string('permanent_pincode', 6);

            $table->enum('admission_type', ['Regular', 'Lateral Entry', 'Transfer']);
            $table->string('exam_name', 100);
            $table->string('board_name', 100);
            $table->string('institution_name', 100);
            $table->decimal('max_marks', 5, 2);
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('percentage', 5, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
