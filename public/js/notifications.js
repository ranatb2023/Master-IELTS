// Notification Helper Functions
function markAsRead(notificationId, actionUrl) {
    // Determine the correct route based on current path
    let routePrefix = '';
    if (window.location.pathname.includes('/admin/')) {
        routePrefix = '/admin';
    } else if (window.location.pathname.includes('/student/')) {
        routePrefix = '/student';
    } else if (window.location.pathname.includes('/tutor/')) {
        routePrefix = '/tutor';
    }

    // Mark as read via AJAX
    fetch(`${routePrefix}/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && actionUrl && actionUrl !== '#') {
                // Redirect to the notification's action URL
                window.location.href = actionUrl;
            } else {
                // Just reload the page
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            // Redirect anyway
            if (actionUrl && actionUrl !== '#') {
                window.location.href = actionUrl;
            }
        });
}

// Poll for new notifications every 30 seconds
let notificationPollInterval;

function startNotificationPolling() {
    // Initial poll
    updateNotificationCount();

    // Poll every 30 seconds
    notificationPollInterval = setInterval(updateNotificationCount, 30000);
}

function updateNotificationCount() {
    fetch('/api/notifications/unread-count', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(response => response.json())
        .then(data => {
            const badges = document.querySelectorAll('.notification-badge, [class*="notification"] span[class*="bg-red"]');
            badges.forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            });
        })
        .catch(error => {
            console.error('Error fetching notification count:', error);
        });
}

// Start polling when page loads
document.addEventListener('DOMContentLoaded', function () {
    // Only start polling if user is authenticated (check for CSRF token)
    if (document.querySelector('meta[name="csrf-token"]')) {
        startNotificationPolling();
    }
});

// Stop polling when page is hidden/closed
document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        if (notificationPollInterval) {
            clearInterval(notificationPollInterval);
        }
    } else {
        startNotificationPolling();
    }
});
