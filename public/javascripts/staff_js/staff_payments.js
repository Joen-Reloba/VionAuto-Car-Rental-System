document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("paymentsTableBody");
    const modal = document.getElementById("paymentModalOverlay");
    const modalClose = document.getElementById("paymentModalClose");

    // Populate table
    function populateTable(data) {
        tableBody.innerHTML = "";

        if (!data || data.length === 0) {
            tableBody.innerHTML =
                '<tr><td colspan="8" style="text-align: center; padding: 20px;">No payments found</td></tr>';
            return;
        }

        data.forEach((payment) => {
            const statusClass = payment.status.toLowerCase();
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${payment.booking_ID}</td>
                <td>
                    <div class="customer-name">
                        <span>${payment.customer_name}</span>
                    </div>
                </td>
                <td>${payment.vehicle_name}</td>
                <td>${payment.rent_start} - ${payment.rent_end}</td>
                <td>₱${formatCurrency(payment.amount_due)}</td>
                <td>${payment.payment_type}</td>
                <td><span class="status-badge ${statusClass}">${payment.status.charAt(0).toUpperCase() + payment.status.slice(1)}</span></td>
                <td>
                <div class="action-buttons">
                    ${
                        payment.status === "verified" ||
                        payment.status === "rejected"
                            ? `<button class="action-btn" disabled style="background:#e5e7eb;color:#9ca3af;cursor:not-allowed;border:1px solid #d1d5db;box-shadow:none;transform:none;">
                            ${payment.status === "verified" ? "✓ Verified" : "✗ Rejected"}
                        </button>`
                            : `<button class="action-btn verify-btn" title="Verify" onclick="verifyPayment('${payment.payment_ID}')">
                            Verify
                        </button>`
                    }
                </div>
            </td>
            `;

            tableBody.appendChild(row);
        });
    }

    // Format currency
    function formatCurrency(amount) {
        return parseFloat(amount)
            .toFixed(2)
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // View payment details
    window.verifyPayment = function (paymentId) {
        const payment = paymentsData.find((p) => p.payment_ID == paymentId);

        if (payment) {
            // Populate modal
            document.getElementById("modalPaymentBookingID").textContent =
                payment.booking_ID;
            document.getElementById("modalPaymentCustomerName").textContent =
                payment.customer_name;
            document.getElementById("modalPaymentVehicle").textContent =
                payment.vehicle_name;
            document.getElementById("modalPaymentRentalPeriod").textContent =
                payment.rent_start + " - " + payment.rent_end;
            document.getElementById("modalPaymentType").textContent =
                payment.payment_type;
            document.getElementById("modalPaymentReference").textContent =
                payment.reference_number || "N/A";
            document.getElementById("modalPaymentAmount").textContent =
                "₱" + formatCurrency(payment.amount_due);
            document.getElementById("modalReceiptImage").src =
                payment.receipt_image ||
                "https://via.placeholder.com/400x400?text=No+Receipt";
            document.getElementById("verificationNote").value = "";

            // Store current payment ID for approval/rejection
            modal.dataset.paymentId = paymentId;

            // Show modal
            modal.classList.add("active");
        }
    };

    // Approve payment
    window.approvePayment = function () {
        const paymentId = modal.dataset.paymentId;
        const note = document.getElementById("verificationNote").value;

        // Get CSRF token
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";

        // Make request to backend
        fetch("/staff/payments/approve", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                payment_id: paymentId,
                note: note,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message);
                    modal.classList.remove("active");
                    // Reload page to refresh data
                    location.reload();
                } else {
                    alert(
                        "Error: " +
                            (data.message || "Failed to approve payment"),
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error approving payment");
            });
    };

    // Reject payment
    window.rejectPayment = function () {
        const paymentId = modal.dataset.paymentId;
        const note = document.getElementById("verificationNote").value;

        // Get CSRF token
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";

        // Make request to backend
        fetch("/staff/payments/reject", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                payment_id: paymentId,
                note: note,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message);
                    modal.classList.remove("active");
                    // Reload page to refresh data
                    location.reload();
                } else {
                    alert(
                        "Error: " +
                            (data.message || "Failed to reject payment"),
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error rejecting payment");
            });
    };

    // Close modal
    modalClose.addEventListener("click", function () {
        modal.classList.remove("active");
    });

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.remove("active");
        }
    });

    // Filter by status
    window.filterByPaymentStatus = function (status) {
        // Update active tab
        document.querySelectorAll(".tab-filter").forEach((btn) => {
            btn.classList.remove("active");
        });
        event.target.classList.add("active");

        // Filter data
        if (status === "all") {
            populateTable(paymentsData);
        } else {
            const filtered = paymentsData.filter(
                (p) => p.status.toLowerCase() === status,
            );
            populateTable(filtered);
        }
    };

    // Filter by search
    window.filterPayments = function () {
        const searchTerm = document
            .getElementById("searchInput")
            .value.toLowerCase();

        const filtered = paymentsData.filter(
            (p) =>
                p.customer_name.toLowerCase().includes(searchTerm) ||
                p.vehicle_name.toLowerCase().includes(searchTerm) ||
                p.booking_ID.toString().includes(searchTerm),
        );

        populateTable(filtered);
    };

    // Initial population
    populateTable(paymentsData);
});
