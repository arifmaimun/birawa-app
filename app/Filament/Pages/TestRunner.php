<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class TestRunner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static string $view = 'filament.pages.test-runner';
    
    protected static ?string $navigationLabel = 'System Test';
    
    protected static ?string $title = 'Automated System Testing';
    
    protected static ?string $navigationGroup = 'System';

    public $testRuns = [];

    public function mount()
    {
        $this->loadTestRuns();
    }

    public function loadTestRuns()
    {
        $path = storage_path('app/test_runs');
        $this->testRuns = [];

        if (File::exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                $data = json_decode(File::get($file), true);
                $this->testRuns[] = [
                    'batch_id' => $data['batch_id'] ?? 'Unknown',
                    'timestamp' => $data['timestamp'] ?? 'Unknown',
                    'count' => isset($data['created_ids']) ? count($data['created_ids'], COUNT_RECURSIVE) : 0,
                    'file' => $file->getFilename(),
                    'report_url' => asset('storage/test-reports/report_' . ($data['batch_id'] ?? '') . '.html'),
                ];
            }
        }
        
        // Sort by timestamp desc
        usort($this->testRuns, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });
    }

    public function runTest()
    {
        try {
            Artisan::call('test:run');
            
            Notification::make()
                ->title('Test Suite Completed')
                ->success()
                ->send();
                
            $this->loadTestRuns();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Test Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function cleanData($filename)
    {
        $path = storage_path('app/test_runs/' . $filename);
        if (!File::exists($path)) return;

        $data = json_decode(File::get($path), true);
        
        // Use the logic from the command via a service or just replicate it here
        // Ideally we should use the command with --cleanup but that cleans ALL.
        // We want to clean specific batch.
        
        $this->deleteFromData($data);
        File::delete($path);
        
        Notification::make()
            ->title('Test Data Cleaned')
            ->success()
            ->send();
            
        $this->loadTestRuns();
    }
    
    protected function deleteFromData($data)
    {
        if (!isset($data['created_ids'])) return;
        
        foreach ($data['created_ids'] as $modelClass => $ids) {
            if (class_exists($modelClass)) {
                $modelClass::whereIn('id', $ids)->forceDelete();
            }
        }
    }

    protected function getActions(): array
    {
        return [
            Action::make('run_test')
                ->label('Run New Test')
                ->action('runTest')
                ->color('primary')
                ->icon('heroicon-o-play'),
        ];
    }
}
