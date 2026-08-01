<?php

namespace App\Notifications;

use App\Models\ProjectServer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjectServerExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly ProjectServer $server) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, int|string> */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->server->managed_project_id,
            'project_name' => $this->server->managedProject->name,
            'server_id' => $this->server->id,
            'server_name' => $this->server->name,
            'expires_at' => $this->server->expires_at?->isoFormat('D MMM YYYY') ?? '-',
        ];
    }
}
