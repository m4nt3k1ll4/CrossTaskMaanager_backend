<?php

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Lista de tareas de prueba
        $tasks = [
            [
                'title' => 'Update Website Content',
                'description' => 'Review and update the homepage content for the website.',
                'priority' => 'high',
                'due_date' => '2024-12-01',
            ],
            [
                'title' => 'Create Marketing Plan',
                'description' => 'Develop a marketing strategy for Q1 of 2025.',
                'priority' => 'medium',
                'due_date' => '2024-12-15',
            ],
            [
                'title' => 'Database Optimization',
                'description' => 'Optimize the database queries for the reporting module.',
                'priority' => 'high',
                'due_date' => '2024-11-30',
            ],
            [
                'title' => 'Employee Training',
                'description' => 'Schedule and conduct training sessions for new employees.',
                'priority' => 'low',
                'due_date' => '2025-01-10',
            ],
            [
                'title' => 'Client Feedback Survey',
                'description' => 'Prepare and distribute a feedback survey to all clients.',
                'priority' => 'medium',
                'due_date' => '2024-12-20',
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
