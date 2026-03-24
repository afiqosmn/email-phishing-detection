<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\DetectionResult;
use App\Services\DetectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScanEmailJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3;
    public $backoff = 5; // in seconds

    public function __construct(
        public int $emailId,
        public array $emailData
    ) {}

    public function handle(DetectionService $detectionService)
    {
        $email = Email::findOrFail($this->emailId);

        // Prevent duplicate scan
        if (
            DetectionResult::where('message_id', $email->message_id)->exists()
        ) {
            return;
        }

        $detectionService->analyze(
            $this->emailData,
            $email->message_id
        );

        $email->update([
            'processing_status' => 'scanned',
        ]);
    }
}