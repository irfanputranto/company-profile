<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;

class ProjectNotificationController extends Controller
{
    public function read(string $notification): RedirectResponse
    {
        /** @var DatabaseNotification $item */
        $item = auth()->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        return to_route('project-management.projects.show', $item->data['project_id']);
    }

    public function readAll(): RedirectResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
