<?php

namespace App\Console\Commands;

use App\Models\ProjectServer;
use App\Models\User;
use App\Notifications\ProjectServerExpiryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendProjectServerExpiryReminders extends Command
{
    protected $signature = 'projects:send-server-expiry-reminders';

    protected $description = 'Mengirim pengingat server proyek yang mendekati masa kedaluwarsa';

    public function handle(): int
    {
        $recipients = User::permission('view_managed_projects')->where('is_active', true)->get();
        $sent = 0;

        if ($recipients->isEmpty()) {
            $this->warn('Tidak ada pengguna aktif yang memiliki akses manajemen proyek.');

            return self::SUCCESS;
        }

        ProjectServer::query()
            ->with('managedProject:id,name')
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereNull('last_notified_at')
            ->whereDate('expires_at', '<=', today()->addYear())
            ->chunkById(100, function ($servers) use ($recipients, &$sent): void {
                foreach ($servers as $server) {
                    if ($server->expires_at->startOfDay()->gt(today()->addDays($server->reminder_days))) {
                        continue;
                    }

                    Notification::send($recipients, new ProjectServerExpiryNotification($server));
                    $server->updateQuietly([
                        'last_notified_at' => now(),
                        'status' => $server->expires_at->isPast() ? 'expired' : $server->status,
                    ]);
                    $sent++;
                }
            });

        $this->info("{$sent} pengingat kedaluwarsa server dikirim.");

        return self::SUCCESS;
    }
}
