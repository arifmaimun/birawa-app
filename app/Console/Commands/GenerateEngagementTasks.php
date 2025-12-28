<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visit;
use App\Models\EngagementTask;
use Carbon\Carbon;

class GenerateEngagementTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-engagement-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily engagement tasks for doctors (reminders, wellness checks)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting engagement task generation...');

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $threeDaysAgo = Carbon::today()->subDays(3);

        // 1. Control Reminder (H-1)
        // Find visits scheduled for tomorrow
        $upcomingVisits = Visit::whereDate('scheduled_at', $tomorrow)->get();

        foreach ($upcomingVisits as $visit) {
            EngagementTask::firstOrCreate([
                'doctor_id' => $visit->user_id, // Assuming visit->user_id is the doctor
                'patient_id' => $visit->patient_id,
                'task_type' => 'control',
                'due_date' => $today, // Notify doctor TODAY that tomorrow is control
            ], [
                'status' => 'pending'
            ]);
        }
        $this->info("Generated " . $upcomingVisits->count() . " control reminders.");

        // 2. Wellness Check (H+3 Post-Visit)
        // Find visits completed 3 days ago
        $pastVisits = Visit::where('status', 'completed')
            ->whereDate('scheduled_at', $threeDaysAgo)
            ->get();

        foreach ($pastVisits as $visit) {
            EngagementTask::firstOrCreate([
                'doctor_id' => $visit->user_id,
                'patient_id' => $visit->patient_id,
                'task_type' => 'wellness',
                'due_date' => $today,
            ], [
                'status' => 'pending'
            ]);
        }
        $this->info("Generated " . $pastVisits->count() . " wellness checks.");
        
        // 3. Vaccine Reminders (H-3)
        // This requires 'next_vaccine_date' on Patient or MedicalRecord.
        // Skipping for now as column doesn't strictly exist in provided schema dump.
        
        $this->info('Engagement tasks generation completed.');
    }
}
