<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Community;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentAndCommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing students and communities to clear "other users"
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Student::truncate();
        Community::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $studentsData = [
            [
                'name' => 'Vithu',
                'email' => 'vithu@susl.lk',
                'student_id' => '22FIS0101',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0771112222',
            ],
            [
                'name' => 'Mathurya',
                'email' => 'mathurya@susl.lk',
                'student_id' => '22FIS0102',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0772223333',
            ],
            [
                'name' => 'Mathusa',
                'email' => 'mathusa@susl.lk',
                'student_id' => '22FIS0103',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0773334444',
            ],
            [
                'name' => 'Akshaya',
                'email' => 'akshaya@susl.lk',
                'student_id' => '22FIS0104',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0774445555',
            ],
            [
                'name' => 'Maxi',
                'email' => 'maxi@susl.lk',
                'student_id' => '22FIS0105',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0775556666',
            ],
            [
                'name' => 'Mayoori',
                'email' => 'mayoori@susl.lk',
                'student_id' => '22FIS0106',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0776667777',
            ],
            [
                'name' => 'Kathu',
                'email' => 'kathu@susl.lk',
                'student_id' => '22FIS0107',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0777778888',
            ],
            [
                'name' => 'Yoosuf',
                'email' => 'yoosuf@susl.lk',
                'student_id' => '22FIS0108',
                'faculty' => 'Applied Sciences',
                'year' => 3,
                'phone' => '0778889999',
            ],
        ];

        $students = [];
        foreach ($studentsData as $data) {
            $students[] = Student::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'student_id' => $data['student_id'],
                'faculty' => $data['faculty'],
                'year' => $data['year'],
                'phone' => $data['phone'],
                'password' => Hash::make('password123'), // Simple password for all
            ]);
        }

        // Add some sample community posts from these students
        $posts = [
            [
                'student_name' => 'Vithu',
                'post_content' => 'Hey everyone! Does anyone have the lecture notes for the Advanced Database Systems class from last Tuesday?',
                'description' => 'Academic',
            ],
            [
                'student_name' => 'Mathurya',
                'post_content' => 'Reminder that the registration deadline for the upcoming Tech Hackathon is this Friday. Let\'s form some teams!',
                'description' => 'Events',
            ],
            [
                'student_name' => 'Mathusa',
                'post_content' => 'The library is quite crowded today. Does anyone know if the study rooms on the 3rd floor are open?',
                'description' => 'General',
            ],
            [
                'student_name' => 'Akshaya',
                'post_content' => 'Just completed the Web Development project. If anyone needs help setting up the Laravel backend, feel free to ask!',
                'description' => 'Academic',
            ],
            [
                'student_name' => 'Maxi',
                'post_content' => 'Has anyone tried the new food stall near the East Gate? Highly recommend their fruit juices!',
                'description' => 'General',
            ],
            [
                'student_name' => 'Mayoori',
                'post_content' => 'Looking for one more teammate for our Capstone project. We are working on an AI scheduling assistant.',
                'description' => 'Academic',
            ],
            [
                'student_name' => 'Yoosuf',
                'post_content' => 'Great session at the badminton court today! We play every Monday and Thursday at 5 PM if anyone wants to join.',
                'description' => 'Events',
            ],
        ];

        foreach ($posts as $postData) {
            // Find the student ID for this post
            $student = collect($students)->firstWhere('name', $postData['student_name']);
            if ($student) {
                Community::create([
                    'user_id' => $student->id,
                    'post_content' => $postData['post_content'],
                    'description' => $postData['description'],
                    'is_admin' => false,
                ]);
            }
        }
    }
}
