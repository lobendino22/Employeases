<?php

namespace Database\Seeders;

use App\Models\JobVacancy;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobVacancySeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1; // Assuming admin is user ID 1

        $vacancies = [
            [
                'job_category_id' => 5, // Government
                'title' => 'Municipal Administrative Assistant I',
                'description' => 'The Municipal Administrative Assistant I provides administrative support to the Municipal Mayor\'s Office and other local government departments. Responsibilities include filing, records management, correspondence, and frontline services to constituents.',
                'requirements' => "- Bachelor's degree in Public Administration, Business Administration, or related field\n- Good communication skills\n- Knowledge of government procedures\n- Computer literate\n- Resident of Tagudin, Ilocos Sur preferred",
                'employment_type' => 'full_time',
                'location' => 'Municipal Hall, Tagudin, Ilocos Sur',
                'company' => 'Municipal Government of Tagudin',
                'salary_min' => 15000,
                'salary_max' => 20000,
                'slots_available' => 2,
                'application_deadline' => Carbon::now()->addDays(30),
            ],
            [
                'job_category_id' => 7, // Hospitality
                'title' => 'Hotel Front Desk Clerk',
                'description' => 'Greet guests, handle check-ins and check-outs, answer phone calls, and provide information about local attractions. Must be friendly and professional at all times.',
                'requirements' => "- At least 2 years of college education\n- Good interpersonal skills\n- Fluency in English and Filipino\n- Experience in hospitality is an advantage\n- Can work shifting schedules",
                'employment_type' => 'full_time',
                'location' => 'Tagudin, Ilocos Sur',
                'company' => 'Tagudin Resort Hotel',
                'salary_min' => 12000,
                'salary_max' => 16000,
                'slots_available' => 3,
                'application_deadline' => Carbon::now()->addDays(45),
            ],
            [
                'job_category_id' => 2, // Agriculture
                'title' => 'Agricultural Extension Worker',
                'description' => 'Provide technical assistance to local farmers, conduct training on modern farming techniques, and help implement agricultural programs of the municipality.',
                'requirements' => "- Bachelor's degree in Agriculture or related field\n- Willingness to work in field conditions\n- Good communication skills\n- Knowledge of Ilocano is an advantage",
                'employment_type' => 'contract',
                'location' => 'Tagudin, Ilocos Sur',
                'company' => 'Municipal Agriculture Office',
                'salary_min' => 13000,
                'salary_max' => 18000,
                'slots_available' => 1,
                'application_deadline' => Carbon::now()->addDays(21),
            ],
            [
                'job_category_id' => 6, // Healthcare
                'title' => 'Barangay Health Worker',
                'description' => 'Provide basic health services in the community, assist in vaccination programs, conduct health education, and refer patients to proper health facilities.',
                'requirements' => "- Completion of BHW Training Program\n- Basic knowledge of health and nutrition\n- Good communication skills\n- Resident of the assigned barangay",
                'employment_type' => 'part_time',
                'location' => 'Various Barangays, Tagudin, Ilocos Sur',
                'company' => 'Municipal Health Office',
                'salary_min' => 8000,
                'salary_max' => 10000,
                'slots_available' => 5,
                'application_deadline' => Carbon::now()->addDays(60),
            ],
            [
                'job_category_id' => 4, // Education
                'title' => 'Elementary School Teacher (Contractual)',
                'description' => 'Teach elementary-level subjects in a public school setting. Prepare lesson plans, evaluate student performance, and participate in school activities.',
                'requirements' => "- Bachelor's degree in Elementary Education (BEED)\n- LET Passer preferred\n- Passion for teaching\n- Good classroom management skills",
                'employment_type' => 'contract',
                'location' => 'Tagudin Central School, Tagudin, Ilocos Sur',
                'company' => 'Department of Education - Tagudin District',
                'salary_min' => 15000,
                'salary_max' => 20000,
                'slots_available' => 3,
                'application_deadline' => Carbon::now()->addDays(14),
            ],
            [
                'job_category_id' => 10, // Services
                'title' => 'Store Cashier/Sales Assistant',
                'description' => 'Handle customer transactions, assist customers with their purchases, manage inventory, and ensure store cleanliness.',
                'requirements' => "- At least high school graduate\n- Basic math skills\n- Honest and trustworthy\n- Can work on weekends and holidays\n- Previous retail experience is an advantage",
                'employment_type' => 'full_time',
                'location' => 'Poblacion, Tagudin, Ilocos Sur',
                'company' => 'Tagudin General Merchandise',
                'salary_min' => 10000,
                'salary_max' => 13000,
                'slots_available' => 2,
                'application_deadline' => Carbon::now()->addDays(30),
            ],
            [
                'job_category_id' => 3, // Construction
                'title' => 'Skilled Construction Worker',
                'description' => 'Perform construction tasks including masonry, carpentry, and basic electrical work for municipal infrastructure projects.',
                'requirements' => "- At least 2 years of construction experience\n- Knowledge of basic construction techniques\n- Physically fit\n- Willing to work on site",
                'employment_type' => 'temporary',
                'location' => 'Tagudin, Ilocos Sur',
                'company' => 'Municipal Engineering Office',
                'salary_min' => 12000,
                'salary_max' => 15000,
                'slots_available' => 10,
                'application_deadline' => Carbon::now()->addDays(7),
            ],
            [
                'job_category_id' => 5, // Government
                'title' => 'Municipal Records Officer',
                'description' => 'Maintain the municipal records system, process document requests, and ensure public records are filed and archived according to government retention rules.',
                'requirements' => "- Bachelor's degree in Public Administration or related field\n- Experience with records management\n- Detail-oriented and organized\n- Computer literate",
                'employment_type' => 'full_time',
                'location' => 'Municipal Hall, Tagudin, Ilocos Sur',
                'company' => 'Municipal Government of Tagudin',
                'salary_min' => 16000,
                'salary_max' => 21000,
                'slots_available' => 1,
                // Closing date has already passed so this posting is archived automatically.
                'application_deadline' => Carbon::create(now()->year, 9, 27, 0, 0, 0),
            ],
            [
                'job_category_id' => 8, // IT
                'title' => 'IT Helpdesk Assistant',
                'description' => 'Provide first-level technical support, log helpdesk tickets, and assist staff with hardware, software, and account issues.',
                'requirements' => "- Associate or Bachelor's degree in IT or related field\n- Basic troubleshooting skills\n- Good customer service attitude\n- Fresh graduates are welcome to apply",
                'employment_type' => 'full_time',
                'location' => 'Tagudin, Ilocos Sur',
                'company' => 'Tagudin Municipal Government',
                'salary_min' => 13000,
                'salary_max' => 17000,
                'slots_available' => 2,
                // Posted today and closes later, so it stays active.
                'application_deadline' => Carbon::now()->addDays(30),
            ],
            [
                'job_category_id' => 8, // IT
                'title' => 'Computer Technician / IT Support',
                'description' => 'Provide technical support for computer systems, networks, and office equipment. Maintain and troubleshoot hardware and software issues.',
                'requirements' => "- Associate or Bachelor's degree in IT or related field\n- Knowledge of computer hardware and software\n- Good problem-solving skills\n- Can work independently",
                'employment_type' => 'full_time',
                'location' => 'Tagudin, Ilocos Sur',
                'company' => 'Tagudin Municipal Government',
                'salary_min' => 14000,
                'salary_max' => 18000,
                'slots_available' => 1,
                'application_deadline' => Carbon::now()->addDays(30),
            ],
        ];

        foreach ($vacancies as $vacancy) {
            JobVacancy::updateOrCreate([
                'title' => $vacancy['title'],
            ], [
                'user_id' => $adminId,
                'job_category_id' => $vacancy['job_category_id'],
                'title' => $vacancy['title'],
                'slug' => Str::slug($vacancy['title']) . '-' . uniqid(),
                'description' => $vacancy['description'],
                'requirements' => $vacancy['requirements'],
                'employment_type' => $vacancy['employment_type'],
                'location' => $vacancy['location'],
                'company' => $vacancy['company'],
                'salary_min' => $vacancy['salary_min'],
                'salary_max' => $vacancy['salary_max'],
                'slots_available' => $vacancy['slots_available'],
                'application_deadline' => $vacancy['application_deadline'],
                'is_active' => true,
                'is_open' => true,
            ]);
        }
    }
}
