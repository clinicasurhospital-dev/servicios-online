<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Mark all unread notifications for current user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }

    /**
     * Mark a single notification as read and redirect if action url exists.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $actionUrl = $notification->data['action_url'] ?? null;

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'action_url' => $actionUrl]);
            }

            if ($actionUrl) {
                return redirect($actionUrl);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}
