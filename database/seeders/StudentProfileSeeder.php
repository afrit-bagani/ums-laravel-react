<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentProfileSeeder extends Seeder
{
    public function run(): void
    {
        $batches = DB::table('batch_master')->get();
        $courses = DB::table('course_master')->get();

        if ($batches->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('Please seed Batches and Courses first!');

            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $batch = $batches->random();
            $course = $courses->random();

            // Registration Number based on Batch (e.g., 2022-26 -> 2022xxxx) (Sequential)
            $batchStartYear = explode('-', $batch->name ?? $batch->code ?? '2022')[0] ?? '2022';

            $lastProfile = DB::table('student_profiles')
                ->where('registration_number', 'like', $batchStartYear.'%')
                ->orderBy('registration_number', 'desc')
                ->first(['registration_number']);

            if ($lastProfile) {
                $lastNumber = (int) substr($lastProfile->registration_number, strlen($batchStartYear));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $registrationNumber = $batchStartYear.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // 1. Create User
            $userId = DB::table('users')->insertGetId([
                'name' => "Student {$i}",
                'login_identifier' => $registrationNumber,
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Create Student Profile
            $studentProfileId = DB::table('student_profiles')->insertGetId([
                'user_id' => $userId,
                'registration_number' => $registrationNumber,
                'status' => 'active',
                'full_name' => "Student {$i}",
                'father_name' => "Father {$i}",
                'mother_name' => "Mother {$i}",
                'gender' => rand(0, 1) ? 'Male' : 'Female',
                'dob' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                'abc_id' => 'ABC'.rand(100000000, 999999999),
                'aadhaar_no' => rand(100000000000, 999999999999),
                'nationality' => 'Indian',
                'mobile_no' => '9'.rand(100000000, 999999999),
                'email' => "student{$i}@example.com",
                'religion' => 'Hindu',
                'caste' => 'General',
                'blood_group' => 'O+',
                'marital_status' => 'Single',
                'annual_family_income' => 500000.00,
                'parent_mobile_no' => '8'.rand(100000000, 999999999),

                'is_blind' => false,
                'is_bpl' => false,
                'is_minority' => false,
                'is_ph' => false,

                'present_address' => "123 Street {$i}",
                'present_city' => 'City',
                'present_country' => 'India',
                'present_state' => 'State',
                'present_district' => 'District',
                'present_pincode' => '70000'.rand(1, 9),

                'permanent_address' => "123 Street {$i}",
                'permanent_city' => 'City',
                'permanent_country' => 'India',
                'permanent_state' => 'State',
                'permanent_district' => 'District',
                'permanent_pincode' => '70000'.rand(1, 9),

                'admission_type' => 'Regular',
                'exam_name' => '12th Board',
                'board_name' => 'CBSE',
                'institution_name' => 'Delhi Public School',
                'max_marks' => 500.00,
                'marks_obtained' => 420.00,
                'percentage' => 84.00,
            ]);

            // 3. Create Paper Selection
            DB::table('student_paper_selections')->insert([
                'student_profile_id' => $studentProfileId,
                'programme_id' => $course->programme_id,
                'course_id' => $course->course_id,
                'batch_id' => $batch->batch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Create Document
            DB::table('student_documents')->insert([
                'student_profile_id' => $studentProfileId,
                'photo_path' => "uploads/photos/{$registrationNumber}.jpg",
                'signature_path' => "uploads/signatures/{$registrationNumber}.jpg",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. Create Payment
            DB::table('student_payments')->insert([
                'student_profile_id' => $studentProfileId,
                'fee_type' => 'Admission Fee',
                'amount' => 50000.00,
                'payment_method' => 'upi',
                'transaction_id' => 'TXN'.rand(1000000000, 9999999999),
                'payment_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
