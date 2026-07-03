<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Timetable;
use Illuminate\Database\Seeder;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        $student = Student::where('email', 'vithu@susl.lk')->first();

        if (! $student) {
            return;
        }

        $entries = [
            ['subject' => 'IS 3110', 'lecturer' => 'Dr. Silva', 'room' => 'Lab 03', 'day' => 'Monday', 'start_time' => '08:00', 'end_time' => '09:00'],
            ['subject' => 'IS 3120', 'lecturer' => 'Dr. Perera', 'room' => 'Room 201', 'day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '10:00'],
            ['subject' => 'IS 3130', 'lecturer' => 'Mr. Fernando', 'room' => 'Lab 01', 'day' => 'Wednesday', 'start_time' => '10:00', 'end_time' => '11:00'],
            ['subject' => 'IS 3210', 'lecturer' => 'Ms. Jayawardena', 'room' => 'Room 105', 'day' => 'Thursday', 'start_time' => '13:00', 'end_time' => '14:00'],
        ];

        foreach ($entries as $entry) {
            Timetable::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'subject' => $entry['subject'],
                    'day' => $entry['day'],
                    'start_time' => $entry['start_time'],
                ],
                [
                    'lecturer' => $entry['lecturer'],
                    'room' => $entry['room'],
                    'end_time' => $entry['end_time'],
                ]
            );
        }
    }
}
