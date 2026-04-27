<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Bookings</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer_css/customer_bookings.css') }}">
</head>
<body>
    @include('layouts.landing_page_navbar')

    <div class="customer-bookings-page">
        <div class="page-header-wrapper">
            <a href="{{ route('landing') }}" class="back-btn">← Back</a>
            <h1 class="page-title">My Bookings</h1>
        </div>

        {{-- Filter Tabs --}}
        <div class="bookings-filters">
            <button class="filter-btn active" data-filter="all" onclick="filterBookings('all')">All</button>
            <button class="filter-btn" data-filter="pending" onclick="filterBookings('pending')">Pending</button>
            <button class="filter-btn" data-filter="approved" onclick="filterBookings('approved')">Approved</button>
            <button class="filter-btn" data-filter="ongoing" onclick="filterBookings('ongoing')">Ongoing</button>
            <button class="filter-btn" data-filter="finished" onclick="filterBookings('finished')">Finished</button>
        </div>

        <div class="bookings-container">
            @forelse($bookings as $booking)
                <div class="booking-card" data-status="{{ strtolower($booking->status) }}" data-payment="{{ strtolower($booking->payment_status) }}" data-booking-id="{{ $booking->booking_ID }}">
                    <div class="booking-header">
                        <div class="booking-info">
                            <h3 class="booking-title">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</h3>
                            <p class="booking-id">Booking ID: #{{ $booking->booking_ID }}</p>
                        </div>
                        <span class="status-badge {{ strtolower($booking->status) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <div class="booking-image">
                        @php
                            $vehicleImage = $booking->vehicle->images->where('is_primary', true)->first() ?? $booking->vehicle->images->first();
                        @endphp
                        @if($vehicleImage && $vehicleImage->img_path)
                            <img src="{{ asset('assets/images/images-vehicles/' . $vehicleImage->img_path) }}" alt="{{ $booking->vehicle->brand }}">
                        @else
                            <div class="image-placeholder">No Image</div>
                        @endif
                    </div>

                    <div class="booking-details">
                        <div class="detail-row">
                            <label>Category:</label>
                            <span>{{ $booking->vehicle->category }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Plate No:</label>
                            <span>{{ $booking->vehicle->plate_no }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Rental Period:</label>
                            <span>{{ $booking->rent_start->format('M d, Y') }} - {{ $booking->rent_end->format('M d, Y') }}</span>
                        </div>
                        @if($booking->returned_at)
                            <div class="detail-row">
                                <label>Returned Date:</label>
                                <span>{{ $booking->returned_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                        <div class="detail-row">
                            <label>Daily Rate:</label>
                            <span>₱{{ number_format($booking->vehicle->daily_rate, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Total Amount:</label>
                            <span class="amount">₱{{ number_format($booking->total, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Downpayment:</label>
                            <span class="amount">₱{{ number_format($booking->downpayment, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Payment Status:</label>
                            <span class="payment-badge {{ strtolower($booking->payment_status) }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="booking-actions">
                        @if($booking->status === 'pending')
                            <button class="action-btn cancel-btn" onclick="cancelBooking({{ $booking->booking_ID }})">Cancel Booking</button>
                        @elseif($booking->status === 'approved' && $booking->payment_status === 'unpaid')
                            <button class="action-btn pay-btn" onclick="makePayment({{ $booking->booking_ID }})">Pay Downpayment</button>
                        @elseif($booking->status === 'ongoing' && $booking->returned_at && $booking->payment_status === 'downpaid')
                            <button class="action-btn fullpay-btn" onclick="makeFullPayment({{ $booking->booking_ID }})">Pay Remaining Balance</button>
                        @elseif($booking->status === 'ongoing')
                            <button class="action-btn view-btn" onclick="viewBookingDetails({{ $booking->booking_ID }})">View Details</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h2>No Bookings Yet</h2>
                    <p>You haven't made any bookings. Start exploring our vehicles!</p>
                    <a href="{{ route('customer.browse-all-vehicles') }}" class="browse-btn">Browse Vehicles</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Payment Modal --}}
   <div id="paymentModal" class="modal modal-hidden">
        <div class="modal-content payment-modal-content">
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
            
            <div class="modal-header">
                <h2>Pay via Gcash</h2>
                <p class="booking-id-modal">Booking ID #<span id="modalBookingId">0001</span></p>
            </div>

            <div class="payment-details-box">
                <div class="detail-item">
                    <span class="detail-label">Car</span>
                    <span class="detail-value" id="modalCarName">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Rental Period</span>
                    <span class="detail-value" id="modalRentalPeriod">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value" id="modalTotalAmount">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Payment type</span>
                    <span class="detail-value">Downpayment</span>
                </div>
            </div>

            <div class="final-payment-box">
                <p class="final-label" id="finalPaymentLabel">Downpayment Due</p>
                <p class="final-amount" id="modalDownpayment">₱0.00</p>
            </div>

            <div class="qr-code-section">
                <p class="qr-instruction">Scan QR code below for payment</p>
                <div class="qr-placeholder">
                    <img src="{{asset('assets/images/sample_qr.png')}}" alt="QR Code">
                </div>
            </div>

            <div class="receipt-upload-section">
                <div class="upload-icon">📱</div>
                <p class="upload-title">Upload Gcash receipt</p>
                <p class="upload-description">Take a screenshot of your Gcash transaction and upload it here as proof of payment</p>
                <div class="file-input-wrapper">
                    <input type="file" id="receiptInput" name="receipt" accept="image/*" style="display: none;">
                    <button type="button" class="upload-btn" onclick="document.getElementById('receiptInput').click()">Choose File</button>
                    <p class="file-name" id="fileName">No file chosen</p>
                </div>
            </div>

            <div class="reference-number-section">
                <label for="referenceNumberInput" class="reference-label">Gcash Reference Number <span class="required">*</span></label>
                <input type="text" id="referenceNumberInput" name="reference_number" class="reference-input" placeholder="e.g., 202604271234567" required>
                <p class="reference-description">Enter the 12-digit reference number from your Gcash receipt</p>
            </div>

            <button class="submit-payment-btn" onclick="submitPayment()">Submit Payment</button>
        </div>
    </div>

    <script>
        let selectedFile = null;
        let currentBookingData = null;

        function filterBookings(status) {
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Show/hide cards based on filter
            const cards = document.querySelectorAll('.booking-card');
            
            cards.forEach(card => {
                if (status === 'all') {
                    card.style.display = '';
                } else {
                    const cardStatus = card.getAttribute('data-status');
                    const paymentStatus = card.getAttribute('data-payment');
                    
                    // Determine effective status
                    let effectiveStatus = cardStatus;
                    if (cardStatus === 'approved' && paymentStatus === 'paid') {
                        effectiveStatus = 'ongoing';
                    }
                    
                    if (effectiveStatus === status) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }

        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                console.log('Cancel booking:', bookingId);
            }
        }

        function makePayment(bookingId) {
            // Find the booking card
            if (!bookingId) return;
            
            // Check for pending payments first
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/customer/check-pending-payment/${bookingId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasPendingPayment) {
                    alert('❌ Payment Pending\n\n' + data.message + '\n\nYour ' + data.paymentType + ' payment is waiting for staff verification.');
                    return;
                }
                
                // No pending payment, proceed with opening the modal
                const bookingCard = document.querySelector(`[data-booking-id="${bookingId}"]`);
                
                if (bookingCard) {
                    // Extract booking information from the card
                    const carName = bookingCard.querySelector('.booking-title').textContent;
                    const rentalPeriod = bookingCard.querySelector('.detail-row:nth-child(3) span:nth-child(2)').textContent;
                    const totalAmount = bookingCard.querySelector('.detail-row:nth-child(5) span.amount').textContent;
                    const downpayment = bookingCard.querySelector('.detail-row:nth-child(6) span.amount').textContent;

                    // Store booking data globally
                    currentBookingData = {
                        bookingId: bookingId,
                        carName: carName,
                        rentalPeriod: rentalPeriod,
                        totalAmount: totalAmount,
                        downpayment: downpayment
                    };

                    // Update modal with booking information
                    document.getElementById('modalBookingId').textContent = bookingId;
                    document.getElementById('modalCarName').textContent = carName;
                    document.getElementById('modalRentalPeriod').textContent = rentalPeriod;
                    document.getElementById('modalTotalAmount').textContent = totalAmount;
                    document.getElementById('modalDownpayment').textContent = downpayment;
                    document.getElementById('finalPaymentLabel').textContent = 'Downpayment Due';

                    // Show modal
                    const modal = document.getElementById('paymentModal');
                    modal.classList.remove('modal-hidden');
                    modal.style.display = 'flex';
                    
                    // Reset file input
                    selectedFile = null;
                    document.getElementById('receiptInput').value = '';
                    document.getElementById('fileName').textContent = 'No file chosen';
                    updateSubmitButtonState();
                }
            })
            .catch(error => {
                console.error('Error checking pending payment:', error);
                alert('Error checking payment status. Please try again.');
            });
        }

        function makeFullPayment(bookingId) {
            if (!bookingId) return;
            
            // Check for pending payments first
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/customer/check-pending-payment/${bookingId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasPendingPayment) {
                    alert('❌ Payment Pending\n\n' + data.message + '\n\nYour ' + data.paymentType + ' payment is waiting for staff verification.');
                    return;
                }
                
                // No pending payment, proceed with opening the modal
                const bookingCard = document.querySelector(`[data-booking-id="${bookingId}"]`);
                if (bookingCard) {
                    const carName = bookingCard.querySelector('.booking-title').textContent;
                    const rentalPeriod = bookingCard.querySelector('.detail-row:nth-child(3) span:nth-child(2)').textContent;
                    const totalAmount = bookingCard.querySelector('.detail-row:nth-child(5) span.amount').textContent;

                    // Calculate remaining balance (total - downpayment)
                    const totalRaw = parseFloat(bookingCard.querySelector('.detail-row:nth-child(5) span.amount').textContent.replace(/[₱,]/g, ''));
                    const downRaw = parseFloat(bookingCard.querySelector('.detail-row:nth-child(6) span.amount').textContent.replace(/[₱,]/g, ''));
                    const remaining = (totalRaw - downRaw).toLocaleString('en-PH', { minimumFractionDigits: 2 });

                    currentBookingData = {
                        bookingId: bookingId,
                        carName: carName,
                        rentalPeriod: rentalPeriod,
                        totalAmount: totalAmount,
                        downpayment: `₱${remaining}`,
                        paymentType: 'final'
                    };

                    document.getElementById('modalBookingId').textContent = bookingId;
                    document.getElementById('modalCarName').textContent = carName;
                    document.getElementById('modalRentalPeriod').textContent = rentalPeriod;
                    document.getElementById('modalTotalAmount').textContent = totalAmount;
                    document.getElementById('modalDownpayment').textContent = `₱${remaining}`;
                    document.getElementById('finalPaymentLabel').textContent = 'Remaining Balance';

                    // Update modal header to reflect full payment
                    document.querySelector('.modal-header h2').textContent = 'Pay Remaining Balance via Gcash';
                    document.querySelector('.payment-details-box .detail-item:last-child .detail-value').textContent = 'Full Payment';

                    const modal = document.getElementById('paymentModal');
                    modal.classList.remove('modal-hidden');
                    modal.style.display = 'flex';

                    selectedFile = null;
                    document.getElementById('receiptInput').value = '';
                    document.getElementById('fileName').textContent = 'No file chosen';
                    updateSubmitButtonState();
                }
            })
            .catch(error => {
                console.error('Error checking pending payment:', error);
                alert('Error checking payment status. Please try again.');
            });
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.style.display = 'none';
            modal.classList.add('modal-hidden');
            selectedFile = null;
            currentBookingData = null;
            document.querySelector('.modal-header h2').textContent = 'Pay via Gcash';
            document.querySelector('.payment-details-box .detail-item:last-child .detail-value').textContent = 'Downpayment';
            document.getElementById('finalPaymentLabel').textContent = 'Downpayment Due';
            document.getElementById('referenceNumberInput').value = '';
        }

        // Handle file selection
        document.addEventListener('DOMContentLoaded', function() {
            const receiptInput = document.getElementById('receiptInput');
            
            if (receiptInput) {
                receiptInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        selectedFile = e.target.files[0];
                        document.getElementById('fileName').textContent = selectedFile.name;
                        updateSubmitButtonState();
                    }
                });
            }

            // Close modal when clicking outside of it
            window.addEventListener('click', function(e) {
                const modal = document.getElementById('paymentModal');
                if (e.target === modal) {
                    closePaymentModal();
                }
            });

            const filterStatus = "{{ $filterStatus }}";
            const highlightBookingId = "{{ $highlightBookingId }}";
            
            if (filterStatus && filterStatus !== 'all') {
                // Trigger filter
                const filterBtn = document.querySelector(`[data-filter="${filterStatus}"]`);
                if (filterBtn) {
                    // Remove active class from all buttons
                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active to clicked button
                    filterBtn.classList.add('active');
                    
                    // Apply filter
                    filterBookings(filterStatus);
                }
            }
            
            // Highlight specific booking if provided
            if (highlightBookingId) {
                const bookingCard = document.querySelector(`[data-booking-id="${highlightBookingId}"]`);
                if (bookingCard) {
                    bookingCard.classList.add('highlighted');
                    bookingCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        function updateSubmitButtonState() {
            const submitBtn = document.querySelector('.submit-payment-btn');
            if (selectedFile) {
                submitBtn.classList.add('active');
            } else {
                submitBtn.classList.remove('active');
            }
        }

        function submitPayment() {
            if (!selectedFile || !currentBookingData) {
                alert('Please upload a receipt to proceed');
                return;
            }

            const referenceNumber = document.getElementById('referenceNumberInput').value.trim();
            if (!referenceNumber) {
                alert('Please enter the Gcash reference number');
                return;
            }

            // Disable submit button
            const submitBtn = document.querySelector('.submit-payment-btn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            // Create FormData
            const formData = new FormData();
            formData.append('booking_id', currentBookingData.bookingId);
            formData.append('receipt', selectedFile);
            formData.append('reference_number', referenceNumber);
            formData.append('payment_type', currentBookingData.paymentType || 'downpayment');

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send to backend
            fetch('{{ route("customer.submit-payment") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                // Check if response is okay, but capture both success and error responses
                return response.json().then(data => ({
                    status: response.status,
                    data: data
                }));
            })
            .then(({ status, data }) => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                
                if (status === 200 && data.success) {
                    alert('✅ Payment submitted successfully!\n\nWaiting for staff verification. Do not submit another payment.');
                    closePaymentModal();
                    location.reload();
                } else {
                    console.error('Payment submission error:', data);
                    alert('❌ Error: ' + (data.message || 'Failed to submit payment'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('❌ Error submitting payment: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }

        function viewBookingDetails(bookingId) {
            console.log('View details for booking:', bookingId);
        }
    </script>

</body>
</html>
