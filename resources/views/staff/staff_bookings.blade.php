@extends('layouts.staff_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/staff_css/staff_bookings.css') }}">
@endsection

@section('content')
<div class="bookings-page">
    <div class="page-header">
        <h1 class="page-title">Booking</h1>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <h3 class="stat-label">Ongoing</h3>
            <p class="stat-number">{{ $stats['ongoing'] }}</p>
        </div>
        <div class="stat-card">
            <h3 class="stat-label">Pending</h3>
            <p class="stat-number">{{ $stats['pending'] }}</p>
        </div>
        <div class="stat-card">
            <h3 class="stat-label">Approved</h3>
            <p class="stat-number">{{ $stats['approved'] }}</p>
        </div>
        <div class="stat-card">
            <h3 class="stat-label">Finished</h3>
            <p class="stat-number">{{ $stats['finished'] }}</p>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="search-section">
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Search booking...">
            <button class="search-btn" onclick="filterBookings()">Search</button>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="bookings-table-container">
        {{-- Tab Filters --}}
        <div class="tab-filters">
            <button class="tab-filter active" data-filter="all" onclick="filterByStatus('all')">All</button>
            <button class="tab-filter" data-filter="pending" onclick="filterByStatus('pending')">Pending</button>
            <button class="tab-filter" data-filter="approved" onclick="filterByStatus('approved')">Approved</button>
            <button class="tab-filter" data-filter="ongoing" onclick="filterByStatus('ongoing')">Ongoing</button>
            <button class="tab-filter" data-filter="rejected" onclick="filterByStatus('rejected')">Rejected</button>
            <button class="tab-filter" data-filter="finished" onclick="filterByStatus('finished')">Finished</button>
        </div>

        {{-- Table --}}
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Name</th>
                    <th>Vehicle</th>
                    <th>Rent Start</th>
                    <th>Rent End</th>
                    <th>Total (w/ VAT)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bookingsTableBody">
                {{-- Will be populated by JavaScript --}}
            </tbody>
        </table>
    </div>
</div>

{{-- Booking Detail Modal --}}
<div class="modal-overlay" id="bookingModalOverlay">
    <div class="booking-modal">
        <button class="modal-close" id="modalClose">&times;</button>

        <div class="modal-content">
            {{-- Vehicle Image --}}
            <div class="modal-vehicle-section">
                <div class="vehicle-image-wrap">
                    <img id="modalVehicleImage" src="" alt="Vehicle" class="vehicle-image">
                </div>
            </div>

            {{-- Booking Details --}}
            <div class="modal-details-section">
                <h2 id="modalBookingTitle" class="modal-title">Booking Details</h2>

                {{-- Customer Info --}}
                <div class="details-group">
                    <h3 class="group-title">Customer Information</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value" id="modalCustomerName"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value" id="modalCustomerPhone"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value" id="modalCustomerEmail"></span>
                        </div>
                    </div>
                </div>

                {{-- Vehicle Info --}}
                <div class="details-group">
                    <h3 class="group-title">Vehicle Information</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Vehicle</span>
                            <span class="detail-value" id="modalVehicleName"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Plate No.</span>
                            <span class="detail-value" id="modalVehiclePlate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Color</span>
                            <span class="detail-value" id="modalVehicleColor"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Category</span>
                            <span class="detail-value" id="modalVehicleCategory"></span>
                        </div>
                    </div>
                </div>

                {{-- Rental Details --}}
                <div class="details-group">
                    <h3 class="group-title">Rental Details</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Rent Start</span>
                            <span class="detail-value" id="modalRentStart"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Rent End</span>
                            <span class="detail-value" id="modalRentEnd"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Days Rented</span>
                            <span class="detail-value" id="modalDaysRented"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Returned Date</span>
                            <span class="detail-value" id="modalReturnedDate">-</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Daily Rate</span>
                            <span class="detail-value" id="modalDailyRate"></span>
                        </div>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="details-group">
                    <h3 class="group-title">Payment Summary</h3>
                    <div class="payment-summary">
                        <div class="payment-item">
                            <span>Subtotal</span>
                            <span id="modalSubtotal"></span>
                        </div>
                        <div class="payment-item">
                            <span>VAT (12%)</span>
                            <span id="modalVAT"></span>
                        </div>
                        <div class="payment-item total">
                            <span>Total</span>
                            <span id="modalTotal"></span>
                        </div>
                    </div>
                </div>

                {{-- Status & Actions --}}
                <div class="modal-actions">
                    <div class="status-row">
                        <span class="status-label">Booking Status:</span>
                        <span class="status-badge" id="modalStatus"></span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">Payment Status:</span>
                        <span class="status-badge" id="modalPaymentStatus"></span>
                    </div>
                </div>

                {{-- Notes Section --}}
                <div class="details-group">
                    <h3 class="group-title">Staff Notes</h3>
                    <textarea 
                        id="modalNotes" 
                        class="notes-textarea" 
                        placeholder="Add any notes about this booking..."
                        rows="4"></textarea>
                </div>

                <div class="modal-action-buttons">
                    <button class="action-btn approve-btn" id="approveBtn">Approve</button>
                    <button class="action-btn reject-btn" id="rejectBtn">Reject</button>
                    <button class="action-btn start-rental-btn" id="startRentalBtn">Start Rental</button>
                    <button class="action-btn return-vehicle-btn" id="returnVehicleBtn">Return Vehicle</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pass data to JS --}}
<script>
    const bookingsData = @json($bookingsData);
    console.log('bookingsData:', bookingsData);
</script>

<script src="{{ asset('javascripts/staff_js/staff_bookings.js') }}"></script>
@endsection