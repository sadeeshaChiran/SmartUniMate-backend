<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news')->insert([
            [
                'title' => 'Semester 4 Registration Open for Batch 22',
                'content' => 'All Batch 22 students of the Faculty of Computing must complete their Semester 4 course registration through the student portal before June 10, 2026. Students are required to select a minimum of 15 credits and a maximum of 21 credits. Contact the Faculty Registrar if you face any issues during registration.',
                'sub_topic' => 'Course Registration',
                'category_id' => 1,
                'date' => '2026-05-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mid Semester Examination Timetable — Batch 22 Computing',
                'content' => 'The Mid Semester Examination for Batch 22 (Faculty of Computing) is scheduled from June 23 to July 4, 2026. The timetable has been uploaded to the student portal. Students must bring their university ID card and examination admission card to all exam halls. No mobile phones allowed.',
                'sub_topic' => 'Examination Schedule',
                'category_id' => 2,
                'date' => '2026-05-22',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'IEEE Student Branch — Hackathon 2026 Registration',
                'content' => 'The SUSL IEEE Student Branch is organising the annual Hackathon 2026 on July 12–13, 2026 at the Faculty of Computing. Theme: "AI for Social Good". Teams of 3–4 members. Registration closes June 30. Cash prizes worth LKR 150,000. Open to all SUSL undergraduates. Register at ieee.susl.ac.lk.',
                'sub_topic' => 'Hackathon',
                'category_id' => 3,
                'date' => '2026-05-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Library Extended Hours During Exam Season',
                'content' => 'The SUSL Main Library will be open until 10:00 PM daily from June 20 to July 10, 2026 to support students during the examination period. The Computing Faculty reading room will also be available 24/7 with valid student ID access.',
                'sub_topic' => 'Library',
                'category_id' => 4,
                'date' => '2026-05-23',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Final Year Project Proposal Submission — Batch 22',
                'content' => 'Batch 22 Final Year Project (FYP) proposals must be submitted to the FYP coordinator by July 15, 2026. Use the official proposal template available on the faculty website. Each group should consist of 2–3 members. Project areas: AI/ML, Cybersecurity, Software Engineering, Data Science, IoT.',
                'sub_topic' => 'Final Year Project',
                'category_id' => 1,
                'date' => '2026-05-24',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Industrial Training Placement — Batch 22 Applications Open',
                'content' => 'Batch 22 students applying for Industrial Training (IT) placements for Semester 5 must submit their CVs and application forms to the Career Guidance Unit by June 20, 2026. Partner companies include Dialog, WSO2, Virtusa, IFS, Calcey Technologies and more. Minimum GPA 2.5 required for some companies.',
                'sub_topic' => 'Industrial Training',
                'category_id' => 1,
                'date' => '2026-05-21',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'WiFi Upgrade Completed at Computing Faculty Lab Block',
                'content' => 'The IT Centre has completed a campus-wide WiFi upgrade at the Faculty of Computing lab block. All labs now support 802.11ax (Wi-Fi 6). Students can connect using their university credentials (student number + portal password). Report connectivity issues to ithelp@susl.ac.lk.',
                'sub_topic' => 'IT Infrastructure',
                'category_id' => 4,
                'date' => '2026-05-18',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'SUSL Inter-Faculty Cricket Tournament 2026',
                'content' => 'The Annual Inter-Faculty Cricket Tournament begins on June 7, 2026. The Faculty of Computing team will play their first match against the Faculty of Management on June 7 at the SUSL main ground. All students are encouraged to support the team. Practice sessions daily at 4:30 PM at the university ground.',
                'sub_topic' => 'Cricket',
                'category_id' => 5,
                'date' => '2026-05-26',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
