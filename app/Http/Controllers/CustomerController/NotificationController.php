<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\BookingNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the logged-in customer
     */
    public function index()
    {
        $userId = Auth::id();

        $notifications = BookingNotification::where('customer_user_id', $userId)
            ->with(['booking', 'booking.vehicle'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();

        $unreadCount = BookingNotification::where('customer_user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($notificationId)
    {
        try {
            $notification = BookingNotification::findOrFail($notificationId);

            // Verify the notification belongs to the authenticated user
            if ($notification->customer_user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'notification' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $userId = Auth::id();

            BookingNotification::where('customer_user_id', $userId)
                ->where('is_read', false)
                ->each(function ($notification) {
                    $notification->markAsRead();
                });

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notifications as read: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single notification details
     */
    public function show($notificationId)
    {
        try {
            $notification = BookingNotification::with(['booking', 'booking.vehicle', 'booking.customer'])
                ->findOrFail($notificationId);

            // Verify the notification belongs to the authenticated user
            if ($notification->customer_user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Mark as read when viewed
            if (!$notification->is_read) {
                $notification->markAsRead();
            }

            return response()->json([
                'success' => true,
                'notification' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notification: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test endpoint to verify the notification table and creation
     */
    public function testCreateNotification()
    {
        try {
            // Get the first booking
            $booking = \App\Models\Booking::first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bookings found to create test notification',
                ]);
            }

            // Create a test notification
            $notification = BookingNotification::create([
                'booking_ID' => $booking->booking_ID,
                'customer_user_id' => $booking->customer_user_id,
                'type' => 'approved',
                'message' => 'TEST NOTIFICATION: Your booking has been approved!',
                'staff_note' => 'This is a test notification',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification created successfully',
                'notification' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating test notification: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
