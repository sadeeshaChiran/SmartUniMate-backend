<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('knowledge_bases')->insert([

            // ───────────── ACADEMICS ─────────────
            [
                'category' => 'Academics',
                'title' => 'Q: What subjects does Batch 22 Computing study in Semester 3?',
                'source' => 'Faculty Handbook 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Academics',
                'title' => 'Q: What is the minimum GPA to pass a semester at SUSL Faculty of Computing?',
                'source' => 'Academic Regulations 2023',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Academics',
                'title' => 'Q: How many credits are required to complete the BSc Computing degree at SUSL?',
                'source' => 'Degree Programme Specification',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ───────────── EXAMINATIONS ─────────────
            [
                'category' => 'Examinations',
                'title' => 'Q: How do I apply for a grade re-check at SUSL?',
                'source' => 'Examination Division',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Examinations',
                'title' => 'Q: What happens if I miss an exam at SUSL?',
                'source' => 'Examination Regulations',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Examinations',
                'title' => 'Q: When are Batch 22 Semester 3 results released?',
                'source' => 'Examination Division',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ───────────── FACULTY CONTACTS ─────────────
            [
                'category' => 'Faculty Contacts',
                'title' => 'Q: Who is the Dean of the Faculty of Computing at SUSL?',
                'source' => 'Faculty Directory 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Faculty Contacts',
                'title' => 'Q: How do I contact the Faculty Registrar of Computing?',
                'source' => 'Faculty Directory 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Faculty Contacts',
                'title' => 'Q: Who is the batch coordinator for Batch 22 Computing?',
                'source' => 'Faculty Directory 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ───────────── CAMPUS MAP ─────────────
            [
                'category' => 'Campus Map',
                'title' => 'Q: Where is the Faculty of Computing located on the SUSL campus?',
                'source' => 'Campus Guide 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Campus Map',
                'title' => 'Q: What are the computer lab timings at the Faculty of Computing?',
                'source' => 'Lab Rules & Regulations',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Campus Map',
                'title' => 'Q: Is there a canteen near the Faculty of Computing?',
                'source' => 'Campus Guide 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ───────────── ADMINISTRATION ─────────────
            [
                'category' => 'Administration',
                'title' => 'Q: How do I get a student ID card at SUSL?',
                'source' => 'Student Affairs Division',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Administration',
                'title' => 'Q: How do I apply for a bursary or financial aid at SUSL?',
                'source' => 'Student Affairs Division',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Administration',
                'title' => 'Q: Where is the SUSL Student Affairs Division office?',
                'source' => 'Campus Guide 2024',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ───────────── DEADLINES ─────────────
            [
                'category' => 'Deadlines',
                'title' => 'Q: What are the key academic deadlines for Batch 22 in 2026?',
                'source' => 'Academic Calendar 2026',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ───────────── IT SUPPORT ─────────────
            [
                'category' => 'IT Support',
                'title' => 'Q: I forgot my student portal password. How do I reset it?',
                'source' => 'IT Centre',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'IT Support',
                'title' => 'Q: How do I connect to the SUSL WiFi?',
                'source' => 'IT Centre',
                'status' => 'published',
                'file_path' => null,
                'url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
