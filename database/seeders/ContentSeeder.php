<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed News Categories
        $catAcademic = NewsCategory::firstOrCreate(['name' => 'Academic']);
        $catEvents = NewsCategory::firstOrCreate(['name' => 'Events']);
        $catGeneral = NewsCategory::firstOrCreate(['name' => 'General']);

        // 2. Seed News
        $newsData = [
            [
                'title' => 'Semester 2 Registration Now Open',
                'content' => 'All students must register for their Semester 2 modules via the LMS portal before the 25th of this month. Late registrations will not be accepted.',
                'sub_topic' => 'Important Deadline',
                'category_id' => $catAcademic->id,
                'date' => Carbon::today()->toDateString(),
            ],
            [
                'title' => 'Tech Symposium 2026',
                'content' => 'Join us for the annual Tech Symposium featuring guest speakers from leading tech companies in Sri Lanka. Register your team for the hackathon!',
                'sub_topic' => 'Annual Event',
                'category_id' => $catEvents->id,
                'date' => Carbon::today()->addDays(2)->toDateString(),
            ],
            [
                'title' => 'Library Hours Extended for Exams',
                'content' => 'The main campus library will remain open until midnight starting next week to accommodate students preparing for final exams.',
                'sub_topic' => 'Campus Facility',
                'category_id' => $catGeneral->id,
                'date' => Carbon::today()->subDays(1)->toDateString(),
            ],
            [
                'title' => 'Guest Lecture: Artificial Intelligence',
                'content' => 'Dr. Silva will be conducting a special guest lecture on the ethical implications of Artificial Intelligence. Venue: Main Auditorium.',
                'sub_topic' => 'Special Lecture',
                'category_id' => $catAcademic->id,
                'date' => Carbon::today()->addDays(5)->toDateString(),
            ],
            [
                'title' => 'Campus Maintenance Notice',
                'content' => 'Please note that the IT Center will be closed for routine maintenance this coming Saturday from 8:00 AM to 12:00 PM.',
                'sub_topic' => 'Maintenance',
                'category_id' => $catGeneral->id,
                'date' => Carbon::today()->subDays(3)->toDateString(),
            ],
        ];

        foreach ($newsData as $data) {
            News::firstOrCreate(['title' => $data['title']], $data);
        }

        // 3. Seed Knowledge Base
        $kbData = [
            [
                'category' => 'Guidelines',
                'title' => 'IS Capstone Project Guidelines 2026',
                'source' => 'Faculty of Applied Sciences',
                'status' => 'Active',
                'file_path' => null,
                'url' => 'https://vle.sab.ac.lk',
            ],
            [
                'category' => 'Handbook',
                'title' => 'Student Handbook V2',
                'source' => 'University Admin',
                'status' => 'Active',
                'file_path' => null,
                'url' => 'https://www.sab.ac.lk/student-handbook',
            ],
            [
                'category' => 'Policy',
                'title' => 'Academic Integrity & Plagiarism Policy',
                'source' => 'Examination Branch',
                'status' => 'Active',
                'file_path' => null,
                'url' => 'https://www.sab.ac.lk/policies',
            ],
            [
                'category' => 'Resources',
                'title' => 'Free Microsoft Azure for Students',
                'source' => 'IT Services',
                'status' => 'Active',
                'file_path' => null,
                'url' => 'https://azure.microsoft.com/en-us/free/students/',
            ],
            [
                'category' => 'Guidelines',
                'title' => 'Medical Certificate Submission Process',
                'source' => 'Medical Center',
                'status' => 'Active',
                'file_path' => null,
                'url' => 'https://www.sab.ac.lk/medical',
            ],
            [
                'category' => 'Format',
                'title' => 'Standard IEEE Report Template',
                'source' => 'Department of Computing',
                'status' => 'Active',
                'file_path' => null,
                'url' => 'https://www.ieee.org/conferences/publishing/templates.html',
            ],
        ];

        foreach ($kbData as $data) {
            KnowledgeBase::firstOrCreate(['title' => $data['title']], $data);
        }
    }
}
