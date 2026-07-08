<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data for testing the application.
     */
    public function run(): void
    {
        // Create demo admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@brainsbond007.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'organization_name' => 'BrainsBond Demo Corp',
                'plan_type' => 'pro',
                'timezone' => 'Asia/Karachi',
            ]
        );

        // Create demo employees
        $employees = [
            [
                'name' => 'Ahmed Khan',
                'email' => 'ahmed@demo.com',
                'age' => 28,
                'phone' => '+92 300 1234567',
                'department' => 'Engineering',
                'designation' => 'Senior Developer',
                'cnic' => '12345-6789012-3',
                'start_working_hour' => '09:00:00',
                'end_working_hour' => '17:00:00',
                'allow_remote' => true,
                'face_images' => '[]',
                'status' => 'active',
            ],
            [
                'name' => 'Sara Ali',
                'email' => 'sara@demo.com',
                'age' => 25,
                'phone' => '+92 301 9876543',
                'department' => 'Design',
                'designation' => 'UI/UX Designer',
                'cnic' => '12345-6789012-4',
                'start_working_hour' => '10:00:00',
                'end_working_hour' => '18:00:00',
                'allow_remote' => false,
                'face_images' => '[]',
                'status' => 'active',
            ],
            [
                'name' => 'Hassan Raza',
                'email' => 'hassan@demo.com',
                'age' => 32,
                'phone' => '+92 333 5551234',
                'department' => 'Engineering',
                'designation' => 'DevOps Engineer',
                'cnic' => '12345-6789012-5',
                'start_working_hour' => '08:00:00',
                'end_working_hour' => '16:00:00',
                'allow_remote' => true,
                'face_images' => '[]',
                'status' => 'active',
            ],
            [
                'name' => 'Fatima Nawaz',
                'email' => 'fatima@demo.com',
                'age' => 27,
                'phone' => '+92 312 4567890',
                'department' => 'Marketing',
                'designation' => 'Marketing Manager',
                'cnic' => '12345-6789012-6',
                'start_working_hour' => '09:00:00',
                'end_working_hour' => '17:00:00',
                'allow_remote' => false,
                'face_images' => '[]',
                'status' => 'active',
            ],
            [
                'name' => 'Usman Tariq',
                'email' => 'usman@demo.com',
                'age' => 30,
                'phone' => '+92 345 7891234',
                'department' => 'Engineering',
                'designation' => 'Backend Developer',
                'cnic' => '12345-6789012-7',
                'start_working_hour' => '09:00:00',
                'end_working_hour' => '18:00:00',
                'allow_remote' => true,
                'face_images' => '[]',
                'status' => 'active',
            ],
        ];

        foreach ($employees as $empData) {
            Employee::firstOrCreate(
                ['email' => $empData['email']],
                array_merge($empData, ['user_id' => $admin->id])
            );
        }

        $this->command->info("Demo data seeded: 1 admin + {$count = count($employees)} employees");
    }
}
