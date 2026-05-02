import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                /* ── Global ── */
                "resources/css/app.css",

                /* ── Landing / Auth ── */
                "resources/css/landing.css",
                "resources/css/auth.css",

                /* ── Admin ── */
                "resources/css/admin_css/admin_global.css",
                "resources/css/admin_css/admin_dashboard.css",
                "resources/css/admin_css/admin_users.css",
                "resources/css/admin_css/admin_reports.css",
                "resources/css/admin_css/admin_customers.css",

                /* ── Staff ── */
                "resources/css/staff_css/staff_global.css",
                "resources/css/staff_css/staff_customers.css",
                "resources/css/staff_css/staff_bookings.css",
                "resources/css/staff_css/staff_vehicles.css",
                "resources/css/staff_css/staff_payment.css",

                /* ── Customer ── */
                "resources/css/customer_css/booking_confirmation.css",
                "resources/css/customer_css/browse_all_vehicles.css",
                "resources/css/customer_css/customer_bookings.css",
                "resources/css/customer_css/customer_payments.css",
                "resources/css/customer_css/customer_profile.css",
                "resources/css/customer_css/customer_registration.css",
                "resources/css/customer_css/view_vehicle.css",

                /* ── JS ── */
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
