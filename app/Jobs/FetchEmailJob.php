<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\User;
use App\Services\GmailService;
use App\Services\EmailParserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class FetchEmailJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3;
    public $backoff = 5; // in seconds

    public function __construct(
        public int $userId,
        public string $messageId
    ) {}

    public function handle(
        GmailService $gmailService,
        EmailParserService $parser
    ) {
        try {
            // Dedup – jimat quota
            if (Email::where('message_id', $this->messageId)->exists()) {
                return;
            }

            $user = User::findOrFail($this->userId);

            // Gmail service auto handle refresh token
            $service = $gmailService->forUser($user);

            $message = $service->users_messages->get('me', $this->messageId, [
                'format' => 'raw',
            ]);

            // Parse email (raw base64 → structured data)
            $parsed = $parser->parse($message->getRaw());

            // Simpan metadata sahaja (privacy-first)
            $email = Email::create([
                'user_id'           => $user->id,
                'message_id'        => $this->messageId,
                'from'              => $parsed['from'] ?? null,
                'subject'           => $parsed['subject'] ?? null,
                'date'              => $parsed['date'] ?? now(),
                'snippet'           => Str::limit(strip_tags($parsed['body'] ?? ''), 200),
                'processing_status' => 'fetched',
            ]);

            // Dispatch scan job (pass parsed content, bukan raw simpan)
            ScanEmailJob::dispatch(
                emailId: $email->id,
                emailData: [
                    'headers' => $parsed['headers'] ?? [],
                    'subject' => $parsed['subject'] ?? '',
                    'body'    => $parsed['body'] ?? '',
                    'urls'    => $parsed['urls'] ?? [],
                ]
            )->onQueue('analysis');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FetchEmailJob failed', [
                'user_id'    => $this->userId,
                'message_id' => $this->messageId,
                'error'      => $e->getMessage(),
                'attempt'    => $this->attempts(),
            ]);
            throw $e; // Let retry handle it
        }
    }
}
