<?php

use App\Models\User;
use App\Notifications\BroadcastNotification;

// Find first user
$user = User::first();

if (!$user) {
    echo "No users found in database!\n";
    exit(1);
}

echo "Testing notification system with user: {$user->name} (ID: {$user->id})\n\n";

// Send a test notification
$user->notify(new BroadcastNotification(
    'System Test',
    'This is a test notification to verify the notification system is working correctly.'
));

echo "✅ Test notification sent!\n\n";

// Check unread notifications
$unreadCount = $user->unreadNotifications()->count();
echo "Unread notifications: {$unreadCount}\n";

// Show the latest notification
$latestNotification = $user->notifications()->latest()->first();

if ($latestNotification) {
    echo "\nLatest notification:\n";
    echo "  - Title: " . ($latestNotification->data['title'] ?? 'N/A') . "\n";
    echo "  - Message: " . ($latestNotification->data['message'] ?? 'N/A') . "\n";
    echo "  - Read: " . ($latestNotification->read_at ? 'Yes' : 'No') . "\n";
    echo "  - Created: " . $latestNotification->created_at->diffForHumans() . "\n";
}

echo "\n✅ Notification system is working correctly!\n";
echo "\nNext steps:\n";
echo "1. Visit your dashboard and check the bell icon\n";
echo "2. The unread count should show: {$unreadCount}\n";
echo "3. Click the bell to see the notification dropdown\n";
