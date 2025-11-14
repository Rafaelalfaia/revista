<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $r)
    {
        $q = $r->user()->notifications()->latest();
        if ($r->string('status')->lower() === 'unread') {
            $q->whereNull('read_at');
        }
        $notifications = $q->paginate(12)->withQueryString();
        return view('autor.notifications.index', compact('notifications'));
    }

    public function show(Request $r, DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === $r->user()->id && $notification->notifiable_type === get_class($r->user()), 403);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $data = $notification->data ?? [];
        $submissionId = $data['submission_id'] ?? null;
        $action = $data['action'] ?? null;

        if (!$action && $submissionId) {
            if (Route::has('autor.submissions.wizard.index')) {
                $action = route('autor.submissions.wizard.index', $submissionId);
            } elseif (Route::has('autor.submissions.wizard')) {
                $action = route('autor.submissions.wizard', $submissionId);
            } elseif (Route::has('autor.submissions.edit')) {
                $action = route('autor.submissions.edit', $submissionId);
            } else {
                $action = url('/autor/submissions/'.$submissionId.'/wizard');
            }
        }

        return $action ? redirect()->to($action) : redirect()->route('autor.notifications.index');
    }

    public function markAsRead(Request $r, DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === $r->user()->id && $notification->notifiable_type === get_class($r->user()), 403);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        return back()->with('ok', 'Notificação marcada como lida.');
    }

    public function markAllAsRead(Request $r)
    {
        $r->user()->unreadNotifications->markAsRead();
        return back()->with('ok', 'Todas as notificações foram marcadas como lidas.');
    }

    public function destroy(Request $r, DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === $r->user()->id && $notification->notifiable_type === get_class($r->user()), 403);
        $notification->delete();
        return back()->with('ok', 'Notificação removida.');
    }



    protected function ownsOrAbort(Request $r, DatabaseNotification $notification): void
    {
        $user = $r->user();
        $ok = $notification->notifiable_id === $user->getKey()
            && $notification->notifiable_type === get_class($user);

        abort_unless($ok, 403);
    }
}
