<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payments</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer_css/customer_payments.css') }}">
</head>
<body>
    @include('layouts.landing_page_navbar')

    <div class="customer-payments-page">
        <div class="page-header-wrapper">
            <a href="{{ route('landing') }}" class="back-btn">← Back</a>
            <h1 class="page-title">My Payments</h1>
        </div>

        {{-- Filter Tabs --}}
        <div class="payments-filters">
            <button class="filter-btn active" data-filter="all" onclick="filterPayments('all')">All</button>
            <button class="filter-btn" data-filter="pending" onclick="filterPayments('pending')">Pending</button>
            <button class="filter-btn" data-filter="verified" onclick="filterPayments('verified')">Verified</button>
            <button class="filter-btn" data-filter="rejected" onclick="filterPayments('rejected')">Rejected</button>
        </div>

        <div class="payments-container">
            @forelse($payments as $payment)
                <div class="payment-card" data-status="{{ strtolower($payment->status) }}">
                    <div class="payment-header">
                        <div class="payment-info">
                            <h3 class="payment-title">{{ $payment->booking->vehicle->brand }} {{ $payment->booking->vehicle->model }}</h3>
                            <p class="payment-id">Payment #{{ $payment->payment_ID }}</p>
                        </div>
                        <span class="status-badge {{ strtolower($payment->status) }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>

                    <div class="payment-details">
                        <div class="detail-row">
                            <label>Booking ID:</label>
                            <span>#{{ $payment->booking->booking_ID }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Payment Type:</label>
                            <span>{{ ucfirst($payment->payment_type) }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Amount Paid:</label>
                            <span class="amount">₱{{ number_format($payment->amount_paid, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Reference Number:</label>
                            <span>{{ $payment->reference_number }}</span>
                        </div>
                        <div class="detail-row">
                            <label>Payment Date:</label>
                            <span>{{ $payment->payment_date->format('M d, Y') }}</span>
                        </div>
                        @if($payment->status === 'verified' && $payment->verified_at)
                        <div class="detail-row">
                            <label>Verified On:</label>
                            <span>{{ $payment->verified_at->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($payment->verifiedBy)
                        <div class="detail-row">
                            <label>Verified By:</label>
                            <span>{{ $payment->verifiedBy->name ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="payment-actions">
                        @if($payment->receipt_image)
                            <a href="{{ asset('assets/images/images-receipts/' . $payment->receipt_image) }}" target="_blank" class="action-btn view-receipt-btn">
                                View Receipt
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">💳</div>
                    <h2>No Payments Yet</h2>
                    <p>You haven't made any payments. Make a booking to get started!</p>
                    <a href="{{ route('customer.bookings') }}" class="browse-btn">View My Bookings</a>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function filterPayments(status) {
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Show/hide cards based on filter
            const cards = document.querySelectorAll('.payment-card');
            
            cards.forEach(card => {
                if (status === 'all') {
                    card.style.display = '';
                } else {
                    const cardStatus = card.getAttribute('data-status');
                    
                    if (cardStatus === status) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }
    </script>
</body>
</html>
