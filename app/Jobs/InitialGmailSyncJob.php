<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\GmailSync;
use App\Models\User;
use App\Services\GmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class InitialGmailSyncJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300; // avoid infinite loop

    public function __construct(public int $userId) {}

    public function handle()
    {
        $user = User::findOrFail($this->userId);

        // Create sync record to track this operation
        $syncRecord = GmailSync::create([
            'user_id' => $user->id,
            'sync_type' => 'initial',
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $gmail = app(GmailService::class)->forUser($user);

            $pageToken = null;
            $processed = 0;
            $lastMessageId = null;

            do {
                $response = $gmail->users_messages->listUsersMessages('me', [
                    'labelIds'  => ['INBOX'],
                    'maxResults'=> 50,
                    'pageToken'=> $pageToken,
                ]);

                $messages = $response->getMessages() ?? [];

                foreach ($messages as $msg) {
                    $msgId = $msg->getId();
                    $lastMessageId = $msgId;

                    // Dispatch fetch job per email (decouple heavy work)
                    FetchEmailJob::dispatch(
                        userId: $user->id,
                        messageId: $msgId
                    )->onQueue('emails');

                    $processed++;
                }

                $pageToken = $response->getNextPageToken();

            } while ($pageToken);

            // Save marker for future incremental fetch
            $user->update([
                'gmail_synced_at' => now(),
                'gmail_page_token' => $pageToken, // optional
            ]);

            // Update sync record with completion status
            $syncRecord->update([
                'emails_fetched' => $processed,
                'last_message_id' => $lastMessageId,
                'completed_at' => now(),
                'status' => 'completed',
            ]);

            Log::info("Initial Gmail sync completed", [
                'user_id' => $user->id,
                'emails_fetched' => $processed,
                'sync_id' => $syncRecord->id,
            ]);

        } catch (\Exception $e) {
            // Mark sync as failed
            $syncRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error("Initial Gmail sync failed", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'sync_id' => $syncRecord->id,
            ]);

            throw $e;
        }
    }
}
