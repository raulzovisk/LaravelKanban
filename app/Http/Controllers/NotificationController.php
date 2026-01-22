<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lista todas as notificações
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marca notificação como lida
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida!'
        ]);
    }

    /**
     * Marca todas como lidas
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram marcadas como lidas!'
        ]);
    }

    /**
     * Remove notificação
     */
    public function destroy($id)
    {
        Auth::user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificação deletada!'
        ]);
    }
}
