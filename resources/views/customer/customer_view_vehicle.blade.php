<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vehicle['brand'] }} {{ $vehicle['model'] }} - VionAuto</title>
   @vite(['resources/css/customer_css/view_vehicle.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    @include('layouts.landing_page_navbar')

    <div class="vehicle-detail-container">
        <!-- Back Button -->
        <a href="javascript:history.back()" class="back-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>Back</span>
        </a>

        <div class="vehicle-detail-content">
            <!-- Left Side - Images -->
            <div class="vehicle-images-section">
                <!-- Main Image -->
                <div class="main-image-wrapper">
                    @php
                        $primaryImage = collect($vehicle['images'])->firstWhere('is_primary', true);
                        $mainImage = $primaryImage ?? ($vehicle['images'][0] ?? null);
                    @endphp
                    @if($mainImage)
                        <img src="{{ asset('assets/images/images-vehicles/' . $mainImage['path']) }}" alt="{{ $vehicle['brand'] }} {{ $vehicle['model'] }}" class="main-image" id="mainImage">
                    @else
                        <div class="no-image-placeholder">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Thumbnail Gallery -->
                @if(count($vehicle['images']) > 0)
                    <div class="thumbnail-gallery">
                        @foreach($vehicle['images'] as $image)
                            <div class="thumbnail-wrapper">
                                <img src="{{ asset('assets/images/images-vehicles/' . $image['path']) }}" alt="Thumbnail" class="thumbnail" onclick="changeMainImage(this)">
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!empty($vehicle['description']))
                <div class="vehicle-description-section">
                    <h4 class="description-title">About this vehicle</h4>
                    <p class="description-text">{{ $vehicle['description'] }}</p>
                </div>
                 @endif
            </div>

            <!-- Right Side - Booking Card -->
            <div class="booking-card">
                <h2 class="card-title">Car Rental Price</h2>

                <!-- Vehicle Info -->
                <div class="vehicle-info-header">
                    <div class="vehicle-name-section">
                        <h3 class="vehicle-name">{{ $vehicle['brand'] }} {{ $vehicle['model'] }}</h3>
                        <div class="vehicle-details">
                            <span class="detail-label">{{ ucfirst($vehicle['category']) }}</span>
                            <span class="detail-separator">•</span>
                            <span class="detail-label">{{ $vehicle['color'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Daily Rate -->
                <div class="daily-rate-section">
                    <span class="rate-label">Daily Rate</span>
                    <span class="rate-amount">₱{{ number_format($vehicle['daily_rate'], 2) }}</span>
                </div>

                <!-- Trip Dates Form -->
                <form id="bookingForm" class="booking-form">
                    <!-- Trip Start Date -->
                    <div class="form-group">
                        <label for="tripStart" class="form-label">Trip Start</label>
                        <div class="date-input-wrapper">
                            <input type="date" id="tripStart" name="trip_start" class="form-input" required>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>

                    <!-- Trip End Date -->
                    <div class="form-group">
                        <label for="tripEnd" class="form-label">Trip End</label>
                        <div class="date-input-wrapper">
                            <input type="date" id="tripEnd" name="trip_end" class="form-input" required>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>

                    <!-- Downpayment Display -->
                    <div class="downpayment-section">
                        <div class="downpayment-label">
                            <span>Downpayment</span>
                            <span class="downpayment-percentage">(30%)</span>
                        </div>
                        <span class="downpayment-amount">₱<span id="downpaymentAmount">0.00</span></span>
                    </div>

                    <!-- Total Display -->
                    <div class="total-section">
                        <span class="total-label">Total</span>
                        <span class="total-amount">₱<span id="totalAmount">0.00</span></span>
                    </div>

                    <!-- Book Now Button -->
                    <button type="submit" class="book-now-btn">Book Now</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <h3 class="footer-brand">VionAuto</h3>
                <p class="footer-tagline">Driven by Comfort, Powered by Trust.</p>
                <p class="footer-location">Toril, Davao City, Philippines</p>
                <p class="footer-contact">Contact us @ <a href="mailto:vionauto2026@gmail.com">vionauto2026@gmail.com</a></p>
            </div>

            <div class="footer-right">
                <p class="footer-follow">Follow us on</p>
                <div class="footer-socials">
                    <a href="#" class="social-icon" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-icon" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 VionAuto. All rights reserved.</p>
        </div>
    </footer>

    <!-- Include Booking Confirmation Modal -->
    @include('customer.customer_booking_confirmation')

    <script>
        const dailyRate = {{ $vehicle['daily_rate'] }};
        const tripStartInput = document.getElementById('tripStart');
        const tripEndInput = document.getElementById('tripEnd');
        const downpaymentAmountSpan = document.getElementById('downpaymentAmount');
        const totalAmountSpan = document.getElementById('totalAmount');

        // Change main image when thumbnail is clicked
        function changeMainImage(thumbnail) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = thumbnail.src;
        }

        // Calculate and update totals
        function calculateTotals() {
            const startDate = new Date(tripStartInput.value);
            const endDate = new Date(tripEndInput.value);

            if (!tripStartInput.value || !tripEndInput.value) {
                downpaymentAmountSpan.textContent = '0.00';
                totalAmountSpan.textContent = '0.00';
                return;
            }

            if (endDate <= startDate) {
                downpaymentAmountSpan.textContent = '0.00';
                totalAmountSpan.textContent = '0.00';
                return;
            }

            // Calculate days (inclusive)
            const timeDifference = endDate.getTime() - startDate.getTime();
            const daysCount = Math.ceil(timeDifference / (1000 * 3600 * 24));

            // Calculate total and downpayment
            const total = daysCount * dailyRate;
            const downpayment = total * 0.30;

            downpaymentAmountSpan.textContent = downpayment.toFixed(2);
            totalAmountSpan.textContent = total.toFixed(2);
        }

        // Event listeners for date inputs
        tripStartInput.addEventListener('change', calculateTotals);
        tripEndInput.addEventListener('change', calculateTotals);

        // Open booking confirmation modal
        function openBookingModal() {
            const startDate = new Date(tripStartInput.value);
            const endDate = new Date(tripEndInput.value);

            if (!tripStartInput.value || !tripEndInput.value) {
                alert('Please select both trip start and end dates');
                return;
            }

            if (endDate <= startDate) {
                alert('Trip end date must be after trip start date');
                return;
            }

            // Calculate days
            const timeDifference = endDate.getTime() - startDate.getTime();
            const daysCount = Math.ceil(timeDifference / (1000 * 3600 * 24));
            const total = daysCount * dailyRate;
            const downpayment = total * 0.30;

            // Format dates
            const startDateFormatted = tripStartInput.value.split('-').reverse().join('/');
            const endDateFormatted = tripEndInput.value.split('-').reverse().join('/');

            // Update modal content
            document.getElementById('modalStartDate').textContent = startDateFormatted;
            document.getElementById('modalEndDate').textContent = endDateFormatted;
            document.getElementById('modalDays').textContent = daysCount + ' day(s)';
            document.getElementById('modalTotal').textContent = total.toFixed(2);
            document.getElementById('modalDownpayment').textContent = downpayment.toFixed(2);

            // Populate hidden form fields
            document.getElementById('vehicleIdInput').value = {{ $vehicle['vehicle_ID'] }};
            document.getElementById('tripStartInput').value = tripStartInput.value;
            document.getElementById('tripEndInput').value = tripEndInput.value;
            document.getElementById('totalInput').value = total.toFixed(2);
            document.getElementById('downpaymentInput').value = downpayment.toFixed(2);

            // Show modal
            document.getElementById('bookingModal').classList.add('active');
        }

        // Close booking modal
        function closeBookingModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }

        // Form submission for booking confirmation
        document.getElementById('bookingSubmitForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const customerName = document.getElementById('customerName')?.value || '{{ Auth::user()->name ?? '' }}';
            
            if (!customerName || customerName.trim() === '') {
                alert('Please enter your name');
                return;
            }

            document.getElementById('customerNameInput').value = customerName;

            // Submit booking via AJAX
            const formData = new FormData(this);
            
            fetch('{{ route("customer.booking.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking created successfully! Your booking ID is: ' + data.booking_id + '\n\nPlease wait for staff approval.');
                    closeBookingModal();
                    // Optionally redirect to a booking confirmation page
                    // window.location.href = '/customer/booking/' + data.booking_id;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the booking. Please try again.');
            });
        });

        // Form submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            @auth
                openBookingModal();
            @else
                window.location.href = '{{ route('login') }}';
            @endauth
        });
    </script>
</body>
</html>
