<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncLatestEmailJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    public function handle(GmailService $gmailService)
    {
        // Ambil user yang dah pernah sync
        User::whereNotNull('created_at')
            ->chunkById(50, function ($users) use ($gmailService) {
                foreach ($users as $user) {
                    $this->syncUser($user, $gmailService);
                }
            });
    }

    private function syncUser(User $user, GmailService $gmailService): void
    {
        try {
            $service = $gmailService->forUser($user);

            $after = $user->created_at->timestamp;
            $pageToken = null;
            $fetched = 0;

            do {
                $response = $service->users_messages->listUsersMessages('me', [
                    'labelIds'   => ['INBOX'],
                    'q'          => "after:$after",
                    'maxResults' => 20, // small batch → quota-safe
                    'pageToken'  => $pageToken,
                ]);

                $messages = $response->getMessages() ?? [];

                foreach ($messages as $msg) {
                    FetchEmailJob::dispatch(
                        userId: $user->id,
                        messageId: $msg->getId()
                    )->onQueue('emails');

                    $fetched++;
                }

                $pageToken = $response->getNextPageToken();

            } while ($pageToken);

            if ($fetched > 0) {
                $user->update([
                    'gmail_synced_at' => now(),
                ]);
            }

            Log::info('Latest Gmail sync', [
                'user_id' => $user->id,
                'emails'  => $fetched,
            ]);
        } catch (\Exception $e) {
            Log::error('Sync failed for user - continuing with next user', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            // Don't throw - continue processing other users
        }
    }
}
