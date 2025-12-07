<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class NotificationService
{
    const SESSION_KEY = 'dashboard_notifications';
    const DURATION_MINUTES = 5;

    /**
     * Add a notification to session
     */
    public static function add(string $title, string $body = '', string $type = 'success'): void
    {
        $notifications = Session::get(self::SESSION_KEY, []);
        
        $notifications[] = [
            'id' => uniqid('notif_', true),
            'title' => $title,
            'body' => $body,
            'type' => $type, // success, danger, warning, info
            'created_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(self::DURATION_MINUTES)->timestamp,
        ];
        
        Session::put(self::SESSION_KEY, $notifications);
    }

    /**
     * Get all active notifications
     */
    public static function getAll(): array
    {
        $notifications = Session::get(self::SESSION_KEY, []);
        $now = now()->timestamp;
        
        // Filter out expired notifications
        $active = array_filter($notifications, function ($notification) use ($now) {
            return $notification['expires_at'] > $now;
        });
        
        // Update session with only active notifications
        Session::put(self::SESSION_KEY, array_values($active));
        
        return array_values($active);
    }

    /**
     * Clear all notifications
     */
    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Clear expired notifications
     */
    public static function clearExpired(): void
    {
        $notifications = Session::get(self::SESSION_KEY, []);
        $now = now()->timestamp;
        
        $active = array_filter($notifications, function ($notification) use ($now) {
            return $notification['expires_at'] > $now;
        });
        
        Session::put(self::SESSION_KEY, array_values($active));
    }

    /**
     * Get notification count
     */
    public static function count(): int
    {
        return count(self::getAll());
    }
}

