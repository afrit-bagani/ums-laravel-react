<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure UG, PG, PHD programmes exist so we can attach courses to them
        $ugId = DB::table('programme_master')->where('code', 'UG')->value('programme_id') ?? 
                DB::table('programme_master')->insertGetId(['code' => 'UG', 'name' => 'Undergraduate', 'created_at' => now(), 'updated_at' => now()]);
                
        $pgId = DB::table('programme_master')->where('code', 'PG')->value('programme_id') ?? 
                DB::table('programme_master')->insertGetId(['code' => 'PG', 'name' => 'Postgraduate', 'created_at' => now(), 'updated_at' => now()]);
                
        $phdId = DB::table('programme_master')->where('code', 'PHD')->value('programme_id') ?? 
                 DB::table('programme_master')->insertGetId(['code' => 'PHD', 'name' => 'Doctorate (Ph.D.)', 'created_at' => now(), 'updated_at' => now()]);

        // List of 25 courses covering CSE, Nursing, Hospital Management, Hotel Management, Pharmacy
        $courses = [
            // Engineering (UG, PG, PHD)
            ['programme_id' => $ugId, 'code' => 'BTECH-CSE', 'name' => 'B.Tech Computer Science and Engineering'],
            ['programme_id' => $pgId, 'code' => 'MTECH-CSE', 'name' => 'M.Tech Computer Science and Engineering'],
            ['programme_id' => $phdId, 'code' => 'PHD-CSE', 'name' => 'Ph.D. in Computer Science'],
            ['programme_id' => $ugId, 'code' => 'BTECH-ECE', 'name' => 'B.Tech Electronics and Communication'],
            ['programme_id' => $pgId, 'code' => 'MTECH-ECE', 'name' => 'M.Tech Electronics and Communication'],
            ['programme_id' => $ugId, 'code' => 'BTECH-MECH', 'name' => 'B.Tech Mechanical Engineering'],
            
            // Nursing & Medical
            ['programme_id' => $ugId, 'code' => 'BSC-NURSING', 'name' => 'B.Sc Nursing'],
            ['programme_id' => $pgId, 'code' => 'MSC-NURSING', 'name' => 'M.Sc Nursing'],
            ['programme_id' => $phdId, 'code' => 'PHD-NURSING', 'name' => 'Ph.D. in Nursing'],
            
            // Pharmacy
            ['programme_id' => $ugId, 'code' => 'BPHARM', 'name' => 'Bachelor of Pharmacy'],
            ['programme_id' => $pgId, 'code' => 'MPHARM', 'name' => 'Master of Pharmacy'],
            ['programme_id' => $phdId, 'code' => 'PHD-PHARM', 'name' => 'Ph.D. in Pharmacy'],
            
            // Hospital Management
            ['programme_id' => $ugId, 'code' => 'BBA-HM', 'name' => 'BBA in Hospital Management'],
            ['programme_id' => $pgId, 'code' => 'MBA-HM', 'name' => 'MBA in Hospital Management'],
            ['programme_id' => $phdId, 'code' => 'PHD-HA', 'name' => 'Ph.D. in Hospital Administration'],
            
            // Hotel Management
            ['programme_id' => $ugId, 'code' => 'BHM', 'name' => 'Bachelor of Hotel Management'],
            ['programme_id' => $pgId, 'code' => 'MHM', 'name' => 'Master of Hotel Management'],
            ['programme_id' => $phdId, 'code' => 'PHD-HM', 'name' => 'Ph.D. in Hotel Management'],
            
            // General Science, IT & Arts
            ['programme_id' => $ugId, 'code' => 'BSC-IT', 'name' => 'B.Sc Information Technology'],
            ['programme_id' => $pgId, 'code' => 'MCA', 'name' => 'Master of Computer Applications'],
            ['programme_id' => $ugId, 'code' => 'BCA', 'name' => 'Bachelor of Computer Applications'],
            ['programme_id' => $ugId, 'code' => 'BCOM', 'name' => 'Bachelor of Commerce'],
            ['programme_id' => $pgId, 'code' => 'MCOM', 'name' => 'Master of Commerce'],
            ['programme_id' => $ugId, 'code' => 'BA-ENG', 'name' => 'B.A. English Honors'],
            ['programme_id' => $pgId, 'code' => 'MA-ENG', 'name' => 'M.A. English'],
        ];

        foreach ($courses as $course) {
            DB::table('course_master')->updateOrInsert(
                ['code' => $course['code']],
                [
                    'programme_id' => $course['programme_id'],
                    'name' => $course['name'],
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
