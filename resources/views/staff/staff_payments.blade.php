@extends('layouts.staff_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/staff_css/staff_payment.css') }}">
@endsection

@section('content')
<div class="payment-page">
    <div class="page-header">
        <h1 class="page-title">Payment Verification</h1>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <h3 class="stat-label">Pending Verification</h3>
            <p class="stat-number">{{ $stats['pending'] }}</p>
        </div>
        <div class="stat-card">
            <h3 class="stat-label">Verified</h3>
            <p class="stat-number">{{ $stats['verified'] }}</p>
        </div>
        <div class="stat-card">
            <h3 class="stat-label">Rejected</h3>
            <p class="stat-number">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="search-section">
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Search payment...">
            <button class="search-btn" onclick="filterPayments()">Search</button>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="payments-table-container">
        {{-- Tab Filters --}}
        <div class="tab-filters">
            <button class="tab-filter active" data-filter="all" onclick="filterByPaymentStatus('all')">All</button>
            <button class="tab-filter" data-filter="pending" onclick="filterByPaymentStatus('pending')">Pending</button>
            <button class="tab-filter" data-filter="verified" onclick="filterByPaymentStatus('verified')">Verified</button>
            <button class="tab-filter" data-filter="rejected" onclick="filterByPaymentStatus('rejected')">Rejected</button>
        </div>

        {{-- Table --}}
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Rental Period</th>
                    <th>Payment Amount</th>
                    <th>Payment Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="paymentsTableBody">
                {{-- Will be populated by JavaScript --}}
            </tbody>
        </table>
    </div>
</div>

{{-- Payment Verification Modal --}}
<div class="modal-overlay" id="paymentModalOverlay">
    <div class="payment-modal">
        <button class="modal-close" id="paymentModalClose">&times;</button>

        <div class="modal-content">
            {{-- Left Side: Payment Info Card --}}
            <div class="payment-info-section">
                <h2 class="payment-modal-title">Payment Verification</h2>
                
                <div class="payment-info-card">
                    <div class="payment-info-item">
                        <span class="info-label">Booking ID</span>
                        <span class="info-value" id="modalPaymentBookingID"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Customer Name</span>
                        <span class="info-value" id="modalPaymentCustomerName"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Car</span>
                        <span class="info-value" id="modalPaymentVehicle"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Rental Period</span>
                        <span class="info-value" id="modalPaymentRentalPeriod"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Payment Type</span>
                        <span class="info-value" id="modalPaymentType"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Reference Number</span>
                        <span class="info-value" id="modalPaymentReference"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Total Booking Amount</span>
                        <span class="info-value" id="modalTotalBookingAmount"></span>
                    </div>
                    <div class="payment-info-item amount-item">
                        <span class="info-label">Payment Due</span>
                        <span class="info-value amount-value" id="modalPaymentAmount"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Amount Submitted</span>
                        <span class="info-value" id="modalAmountSubmitted"></span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Remaining Balance</span>
                        <span class="info-value" id="modalRemainingBalance"></span>
                    </div>
                </div>
            </div>

            {{-- Right Side: Receipt & Actions --}}
            <div class="receipt-section">
                <div class="receipt-wrapper">
                    <img id="modalReceiptImage" src="" alt="Receipt" class="receipt-image">
                </div>

                <div class="note-section">
                    <label class="note-label">Note</label>
                    <textarea class="note-textarea" id="verificationNote" placeholder="Leave a note for the customer here"></textarea>
                </div>

                <div class="modal-actions">
                    <button class="action-btn approve-btn" onclick="approvePayment()">Approve</button>
                    <button class="action-btn reject-btn" onclick="rejectPayment()">Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pass data to JS --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const paymentsData = @json($paymentsData);
    console.log('paymentsData:', paymentsData);
</script>

<script src="{{ asset('javascripts/staff_js/staff_payments.js') }}"></script>
@endsection
