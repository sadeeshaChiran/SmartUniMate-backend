<?php

namespace Database\Seeders;

use App\Models\AcademicModule;
use Illuminate\Database\Seeder;

class AcademicModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['code' => 'IS 1110', 'name' => 'Introduction to Computing', 'credits' => 3, 'year' => 1, 'faculty' => 'Applied Sciences', 'prereq' => 'None', 'desc' => 'Foundations of computing and programming.'],
            ['code' => 'IS 1120', 'name' => 'Mathematics for Computing I', 'credits' => 3, 'year' => 1, 'faculty' => 'Applied Sciences', 'prereq' => 'None', 'desc' => 'Discrete mathematics and algebra for computing.'],
            ['code' => 'IS 1210', 'name' => 'Programming Fundamentals', 'credits' => 4, 'year' => 1, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 1110', 'desc' => 'Structured programming using a high-level language.'],

            ['code' => 'IS 2110', 'name' => 'Data Structures & Algorithms', 'credits' => 4, 'year' => 2, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 1210', 'desc' => 'Core data structures and algorithm analysis.'],
            ['code' => 'IS 2120', 'name' => 'Database Systems I', 'credits' => 3, 'year' => 2, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 1210', 'desc' => 'Relational databases, SQL, and normalization.'],
            ['code' => 'IS 2210', 'name' => 'Web Technologies', 'credits' => 3, 'year' => 2, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 1210', 'desc' => 'HTML, CSS, JavaScript, and web application basics.'],

            ['code' => 'IS 3110', 'name' => 'Software Engineering', 'credits' => 4, 'year' => 3, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 2110', 'desc' => 'Software development lifecycle, design patterns, and teamwork.'],
            ['code' => 'IS 3120', 'name' => 'Advanced Database Systems', 'credits' => 3, 'year' => 3, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 2120', 'desc' => 'Transactions, indexing, and database administration.'],
            ['code' => 'IS 3130', 'name' => 'Computer Networks', 'credits' => 3, 'year' => 3, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 2110', 'desc' => 'Network layers, protocols, and security fundamentals.'],
            ['code' => 'IS 3210', 'name' => 'Information Systems Analysis', 'credits' => 3, 'year' => 3, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 2210', 'desc' => 'Requirements gathering and systems modeling.'],

            ['code' => 'IS 4110', 'name' => 'Capstone Project I', 'credits' => 6, 'year' => 4, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 3110', 'desc' => 'Research proposal and project planning.'],
            ['code' => 'IS 4120', 'name' => 'Capstone Project II', 'credits' => 6, 'year' => 4, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 4110', 'desc' => 'Implementation, evaluation, and final presentation.'],
            ['code' => 'IS 4130', 'name' => 'Enterprise Application Development', 'credits' => 3, 'year' => 4, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 3110', 'desc' => 'Building scalable enterprise systems.'],
            ['code' => 'IS 4140', 'name' => 'Cloud Computing', 'credits' => 3, 'year' => 4, 'faculty' => 'Applied Sciences', 'prereq' => 'IS 3130', 'desc' => 'Cloud platforms, deployment, and DevOps basics.'],
        ];

        foreach ($modules as $module) {
            AcademicModule::updateOrCreate(
                ['code' => $module['code']],
                $module
            );
        }
    }
}
