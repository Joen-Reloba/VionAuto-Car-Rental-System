document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("bookingsTableBody");
    const modal = document.getElementById("bookingModalOverlay");
    const modalClose = document.getElementById("modalClose");
    const approveBtn = document.getElementById("approveBtn");
    const rejectBtn = document.getElementById("rejectBtn");
    const startRentalBtn = document.getElementById("startRentalBtn");
    const returnVehicleBtn = document.getElementById("returnVehicleBtn");
    const customerMessageGroup = document.getElementById(
        "customerMessageGroup",
    );
    const customerMessageTextarea = document.getElementById("customerMessage");
    const sendCustomerMessageBtn = document.getElementById(
        "sendCustomerMessageBtn",
    );
    let currentBookingId = null;

    // Populate table
    function populateTable(data) {
        tableBody.innerHTML = "";

        if (!data || data.length === 0) {
            tableBody.innerHTML =
                '<tr><td colspan="8" style="text-align: center; padding: 20px;">No bookings found</td></tr>';
            return;
        }

        data.forEach((booking) => {
            const statusClass = booking.status.toLowerCase();
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${booking.booking_ID}</td>
                <td>
                    <div class="customer-name">
                        <span>${booking.customer_name}</span>
                    </div>
                </td>
                <td>${booking.vehicle_name}</td>
                <td>${booking.rent_start}</td>
                <td>${booking.rent_end}</td>
                <td>₱${formatCurrency(booking.total)}</td>
                <td><span class="status-badge ${statusClass}">${booking.status}</span></td>
                <td>
                    <button class="action-btn view-btn" onclick="viewBooking('${booking.booking_ID}')">View Details</button>
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

    // View booking details
    window.viewBooking = function (bookingId) {
        const booking = bookingsData.find((b) => b.booking_ID == bookingId);

        if (booking) {
            currentBookingId = bookingId;

            // Populate modal
            document.getElementById("modalBookingTitle").textContent =
                `Booking #${booking.booking_ID}`;
            document.getElementById("modalVehicleImage").src =
                booking.vehicle_image ||
                "https://via.placeholder.com/400x300?text=No+Image";
            document.getElementById("modalCustomerName").textContent =
                booking.customer_name;
            document.getElementById("modalCustomerPhone").textContent =
                booking.customer_phone;
            document.getElementById("modalCustomerEmail").textContent =
                booking.customer_email;
            document.getElementById("modalVehicleName").textContent =
                booking.vehicle_name;
            document.getElementById("modalVehiclePlate").textContent =
                booking.vehicle_plate;
            document.getElementById("modalVehicleColor").textContent =
                booking.vehicle_color || "N/A";
            document.getElementById("modalVehicleCategory").textContent =
                booking.vehicle_category || "N/A";
            document.getElementById("modalRentStart").textContent =
                booking.rent_start;
            document.getElementById("modalRentEnd").textContent =
                booking.rent_end;
            document.getElementById("modalDaysRented").textContent =
                booking.days_rented + " day(s)";

            // Set returned date if available
            const returnedDateElement =
                document.getElementById("modalReturnedDate");
            if (booking.returned_at) {
                const parts = booking.returned_at.split("/");
                const returnedDate = new Date(
                    parts[2],
                    parseInt(parts[0]) - 1,
                    parts[1],
                );
                returnedDateElement.textContent =
                    returnedDate.toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "short",
                        day: "numeric",
                    });
            } else {
                returnedDateElement.textContent = "-";
            }

            document.getElementById("modalDailyRate").textContent =
                "₱" + formatCurrency(booking.daily_rate);
            document.getElementById("modalSubtotal").textContent =
                "₱" + formatCurrency(booking.subtotal);
            document.getElementById("modalVAT").textContent =
                "₱" + formatCurrency(booking.vat);
            document.getElementById("modalExtraCharge").textContent =
                "₱" + formatCurrency(booking.extra_charge || 0);
            document.getElementById("modalTotal").textContent =
                "₱" + formatCurrency(booking.total);
            document.getElementById("modalDownpayment").textContent =
                "₱" + formatCurrency(booking.downpayment || 0);
            document.getElementById("modalRemainingBalance").textContent =
                "₱" + formatCurrency(booking.remaining_balance || 0);

            const statusBadge = document.getElementById("modalStatus");
            statusBadge.textContent =
                booking.status.charAt(0).toUpperCase() +
                booking.status.slice(1);
            statusBadge.className = `status-badge ${booking.status.toLowerCase()}`;

            // Set payment status
            const paymentStatusBadge =
                document.getElementById("modalPaymentStatus");
            paymentStatusBadge.textContent =
                booking.payment_status.charAt(0).toUpperCase() +
                booking.payment_status.slice(1);
            paymentStatusBadge.className = `status-badge ${booking.payment_status.toLowerCase()}`;

            // Set notes
            const notesTextarea = document.getElementById("modalNotes");
            notesTextarea.value = booking.note || "";

            // Enable/disable notes textarea and buttons based on status
            const isPending = booking.status === "pending";

            // Check if rental can be started (1 day before rent_start through rent_end)
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Parse rent_start date (format: m/d/Y from backend)
            const rentStartParts = booking.rent_start.split("/");
            const rentStartDate = new Date(
                rentStartParts[2],
                parseInt(rentStartParts[0]) - 1,
                rentStartParts[1],
            );
            rentStartDate.setHours(0, 0, 0, 0);

            // Parse rent_end date (format: m/d/Y from backend)
            const rentEndParts = booking.rent_end.split("/");
            const rentEndDate = new Date(
                rentEndParts[2],
                parseInt(rentEndParts[0]) - 1,
                rentEndParts[1],
            );
            rentEndDate.setHours(0, 0, 0, 0);

            // Calculate 1 day before rent start
            const oneDayBefore = new Date(rentStartDate);
            oneDayBefore.setDate(oneDayBefore.getDate() - 1);

            // Can start rental if today >= 1 day before AND today <= rent end day
            const canStartByDate =
                today >= oneDayBefore && today <= rentEndDate;

            const canStartRental =
                booking.status === "approved" &&
                (booking.payment_status === "downpaid" ||
                    booking.payment_status === "fullpaid") &&
                canStartByDate;
            const isOngoing = booking.status === "ongoing";
            const canSendCustomerMessage = [
                "approved",
                "ongoing",
                "finished",
            ].includes(booking.status);

            notesTextarea.disabled = !isPending;
            customerMessageGroup.style.display = canSendCustomerMessage
                ? "block"
                : "none";
            customerMessageTextarea.value = "";
            approveBtn.disabled = !isPending;
            approveBtn.style.display = isPending ? "block" : "none";
            rejectBtn.disabled = !isPending;
            rejectBtn.style.display = isPending ? "block" : "none";
            startRentalBtn.disabled = !canStartRental;
            startRentalBtn.style.display =
                canStartRental || booking.status === "approved"
                    ? "block"
                    : "none";
            const isReturned = !!booking.returned_at;

            if (isReturned) {
                returnVehicleBtn.classList.add("returned");
                returnVehicleBtn.disabled = true;
                returnVehicleBtn.style.display = "block";
                returnVehicleBtn.textContent = `✓ Vehicle Returned`;
                returnVehicleBtn.style.background = "";
                returnVehicleBtn.style.color = "";
                returnVehicleBtn.style.cursor = "";
                returnVehicleBtn.style.border = "";
            } else {
                returnVehicleBtn.classList.remove("returned");
                returnVehicleBtn.disabled = !isOngoing;
                returnVehicleBtn.style.display = isOngoing ? "block" : "none";
                returnVehicleBtn.textContent = "Return Vehicle";
                returnVehicleBtn.style.background = "";
                returnVehicleBtn.style.color = "";
                returnVehicleBtn.style.cursor = "";
                returnVehicleBtn.style.border = "";
            }

            // Show modal
            modal.classList.add("active");
        }
    };

    // Close modal
    modalClose.addEventListener("click", function () {
        modal.classList.remove("active");
        currentBookingId = null;
    });

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.remove("active");
            currentBookingId = null;
        }
    });

    // Approve booking
    approveBtn.addEventListener("click", function () {
        if (!currentBookingId) return;

        const note = document.getElementById("modalNotes").value;

        if (confirm("Are you sure you want to approve this booking?")) {
            fetch(`/staff/bookings/${currentBookingId}/approve`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
                body: JSON.stringify({
                    note: note,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Booking approved successfully!");
                        modal.classList.remove("active");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while approving the booking");
                });
        }
    });

    // Reject booking
    rejectBtn.addEventListener("click", function () {
        if (!currentBookingId) return;

        const note = document.getElementById("modalNotes").value;

        if (confirm("Are you sure you want to reject this booking?")) {
            fetch(`/staff/bookings/${currentBookingId}/reject`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
                body: JSON.stringify({
                    note: note,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Booking rejected successfully!");
                        modal.classList.remove("active");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while rejecting the booking");
                });
        }
    });

    // Start rental
    startRentalBtn.addEventListener("click", function () {
        if (!currentBookingId) return;

        if (
            confirm(
                "Are you sure you want to start the rental for this booking?",
            )
        ) {
            fetch(`/staff/bookings/${currentBookingId}/start-rental`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Rental started successfully!");
                        modal.classList.remove("active");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while starting the rental");
                });
        }
    });

    sendCustomerMessageBtn.addEventListener("click", function () {
        if (!currentBookingId) return;

        const message = customerMessageTextarea.value.trim();

        if (!message) {
            alert("Please enter a message before sending.");
            return;
        }

        sendCustomerMessageBtn.disabled = true;
        const originalText = sendCustomerMessageBtn.textContent;
        sendCustomerMessageBtn.textContent = "Sending...";

        fetch(`/staff/bookings/${currentBookingId}/message`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
            },
            body: JSON.stringify({
                message: message,
            }),
        })
            .then((response) =>
                response.json().then((data) => ({
                    status: response.status,
                    data: data,
                })),
            )
            .then(({ status, data }) => {
                if (status === 200 && data.success) {
                    alert("Message sent to customer.");
                    customerMessageTextarea.value = "";
                } else {
                    alert(
                        "Error: " + (data.message || "Unable to send message."),
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred while sending the message.");
            })
            .finally(() => {
                sendCustomerMessageBtn.disabled = false;
                sendCustomerMessageBtn.textContent = originalText;
            });
    });

    // Return vehicle
    returnVehicleBtn.addEventListener("click", function () {
        if (!currentBookingId) return;

        if (confirm("Are you sure you want to return this vehicle?")) {
            fetch(`/staff/bookings/${currentBookingId}/return-vehicle`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(
                            "Vehicle returned successfully! Please collect the remaining payment from the customer.",
                        );
                        modal.classList.remove("active");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while returning the vehicle");
                });
        }
    });

    // Filter by status
    window.filterByStatus = function (status) {
        // Update active tab
        document.querySelectorAll(".tab-filter").forEach((btn) => {
            btn.classList.remove("active");
        });
        event.target.classList.add("active");

        // Filter data
        if (status === "all") {
            populateTable(bookingsData);
        } else {
            const filtered = bookingsData.filter(
                (b) => b.status.toLowerCase() === status,
            );
            populateTable(filtered);
        }
    };

    // Filter by search
    window.filterBookings = function () {
        const searchTerm = document
            .getElementById("searchInput")
            .value.toLowerCase();

        const filtered = bookingsData.filter(
            (b) =>
                b.customer_name.toLowerCase().includes(searchTerm) ||
                b.vehicle_name.toLowerCase().includes(searchTerm) ||
                b.booking_ID.toString().includes(searchTerm),
        );

        populateTable(filtered);
    };

    // Initial population
    populateTable(bookingsData);
});
