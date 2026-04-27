// Mobile Responsive Toggle Functions

// Toggle Sidebar on Mobile
function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const hamburgerBtn = document.querySelector(".hamburger-btn");
    if (sidebar) {
        sidebar.classList.toggle("active");
    }
    if (hamburgerBtn) {
        hamburgerBtn.classList.toggle("active");
    }
}

// Toggle Mobile Nav Menu
function toggleMobileNavMenu() {
    const navLinks = document.querySelector(".nav-links");
    const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
    if (navLinks) {
        navLinks.classList.toggle("active");
    }
    if (mobileMenuBtn) {
        const hamburger = mobileMenuBtn.querySelector(".hamburger");
        if (hamburger) {
            hamburger.classList.toggle("active");
        }
    }
}

// Close sidebar when a link is clicked
document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = document.querySelectorAll(
        ".sidebar-menu a, .sidebar-menu button",
    );
    const navLinks = document.querySelectorAll(".nav-link");
    const sidebar = document.querySelector(".sidebar");
    const navMenu = document.querySelector(".nav-links");

    // Close sidebar when sidebar link is clicked
    sidebarLinks.forEach((link) => {
        link.addEventListener("click", function () {
            if (window.innerWidth < 768) {
                if (sidebar) {
                    sidebar.classList.remove("active");
                }
            }
        });
    });

    // Close nav menu when nav link is clicked
    navLinks.forEach((link) => {
        link.addEventListener("click", function () {
            if (window.innerWidth < 768 && navMenu) {
                navMenu.classList.remove("active");
            }
        });
    });

    // Close sidebar/nav when clicking outside
    document.addEventListener("click", function (event) {
        const sidebar = document.querySelector(".sidebar");
        const hamburgerBtn = document.querySelector(".hamburger-btn");
        const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
        const navContainer = document.querySelector(".navbar-container");

        if (window.innerWidth < 768) {
            // Close sidebar
            if (
                sidebar &&
                !sidebar.contains(event.target) &&
                !hamburgerBtn?.contains(event.target)
            ) {
                sidebar.classList.remove("active");
            }

            // Close nav menu
            if (
                navMenu &&
                !navMenu.contains(event.target) &&
                !mobileMenuBtn?.contains(event.target) &&
                !navContainer?.contains(event.target)
            ) {
                navMenu.classList.remove("active");
            }
        }
    });
});

// Handle window resize
window.addEventListener("resize", function () {
    const sidebar = document.querySelector(".sidebar");
    const navLinks = document.querySelector(".nav-links");

    if (window.innerWidth >= 768) {
        if (sidebar) sidebar.classList.remove("active");
        if (navLinks) navLinks.classList.remove("active");
    }
});
