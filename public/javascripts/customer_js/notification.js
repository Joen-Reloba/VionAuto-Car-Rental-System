// Toggle profile dropdown
function toggleProfileDropdown() {
    const dropdown = document.getElementById("profileDropdown");
    dropdown.classList.toggle("active");
}

// Toggle notifications dropdown
function toggleNotificationsDropdown() {
    const dropdown = document.getElementById("notificationsDropdown");
    dropdown.classList.toggle("active");
    // Refresh notifications when dropdown is opened
    if (dropdown.classList.contains("active")) {
        loadNotifications();
    }
}

// Load and display notifications
function loadNotifications() {
    fetch("/customer/notifications")
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const notificationsList =
                    document.getElementById("notificationsList");
                const badge = document.getElementById("notificationBadge");

                // Update badge
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = "flex";
                } else {
                    badge.style.display = "none";
                }

                // Display notifications
                if (data.notifications && data.notifications.length > 0) {
                    notificationsList.innerHTML = data.notifications
                        .map((notif) => {
                            const typeDisplay = notif.type
                                .replace(/_/g, " ")
                                .split(" ")
                                .map(
                                    (word) =>
                                        word.charAt(0).toUpperCase() +
                                        word.slice(1),
                                )
                                .join(" ");

                            return `
                        <div class="notification-item ${notif.is_read ? "read" : "unread"}" onclick="viewNotificationBooking(${notif.notification_id}, ${notif.booking_ID}, '${notif.type}')">
                            <div class="notification-header">
                                <span class="notification-type ${notif.type}">${typeDisplay}</span>
                                <span class="notification-time">${new Date(notif.created_at).toLocaleDateString()}</span>
                            </div>
                            <p class="notification-message">${notif.message}</p>
                            ${notif.staff_note ? `<p class="notification-note"><strong>Staff Note:</strong> ${notif.staff_note}</p>` : ""}
                        </div>
                    `;
                        })
                        .join("");
                } else {
                    notificationsList.innerHTML =
                        '<p class="empty-notification">No new notifications</p>';
                }
            }
        })
        .catch((error) => {
            console.error("Error loading notifications:", error);
        });
}

// Mark notification as read and navigate to booking
function viewNotificationBooking(notificationId, bookingId, notificationType) {
    // Mark notification as read
    fetch(`/customer/notifications/${notificationId}/read`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN":
                document.querySelector('meta[name="csrf-token"]')?.content ||
                "",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Update notifications (which updates badge)
                loadNotifications();

                // Navigate based on notification type
                if (notificationType.includes("payment")) {
                    // For payment notifications, go to payments page
                    window.location.href = `/customer/payments`;
                } else {
                    // For booking notifications, go to bookings page with status filter
                    window.location.href = `/customer/bookings?status=${notificationType}&booking=${bookingId}`;
                }
            }
        })
        .catch((error) =>
            console.error("Error marking notification as read:", error),
        );
}

// Mark notification as read
function markNotificationAsRead(notificationId) {
    fetch(`/customer/notifications/${notificationId}/read`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN":
                document.querySelector('meta[name="csrf-token"]')?.content ||
                "",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch((error) =>
            console.error("Error marking notification as read:", error),
        );
}

// Close dropdowns when clicking outside
document.addEventListener("click", function (event) {
    const profileSection = event.target.closest(".profile-section");
    const profileDropdown = document.getElementById("profileDropdown");

    const notificationsWrapper = event.target.closest(
        ".notifications-icon-wrapper",
    );
    const notificationsDropdown = document.getElementById(
        "notificationsDropdown",
    );

    if (!profileSection && profileDropdown) {
        profileDropdown.classList.remove("active");
    }

    if (!notificationsWrapper && notificationsDropdown) {
        notificationsDropdown.classList.remove("active");
    }
});

// Load notifications on page load
document.addEventListener("DOMContentLoaded", function () {
    loadNotifications();
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});
