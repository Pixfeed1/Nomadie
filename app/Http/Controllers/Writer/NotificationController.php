<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        
        return view('writer.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    public function getUnread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->latest()->limit(5)->get();
        
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }
}