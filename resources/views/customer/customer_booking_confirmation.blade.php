<!-- Booking Confirmation Modal -->
<div id="bookingModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Booking Confirmation</h2>
            <button class="modal-close" onclick="closeBookingModal()">&times;</button>
        </div>

        <form id="bookingSubmitForm">
            @csrf
            <input type="hidden" id="vehicleIdInput" name="vehicle_ID">
            <input type="hidden" id="tripStartInput" name="trip_start">
            <input type="hidden" id="tripEndInput" name="trip_end">
            <input type="hidden" id="customerNameInput" name="customer_name">
            <input type="hidden" id="totalInput" name="total">
            <input type="hidden" id="downpaymentInput" name="downpayment">

        <div class="modal-body">
            <!-- Vehicle Details -->
            <div class="modal-section">
                <h3 class="section-title">Vehicle Details</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Car</span>
                        <span class="detail-value">{{ $vehicle['brand'] }} {{ $vehicle['model'] }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Category</span>
                        <span class="detail-value">{{ ucfirst($vehicle['category']) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Color</span>
                        <span class="detail-value">{{ $vehicle['color'] }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Daily Rate</span>
                        <span class="detail-value">₱{{ number_format($vehicle['daily_rate'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Rental Details -->
            <div class="modal-section">
                <h3 class="section-title">Rental Details</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Trip Start</span>
                        <span class="detail-value" id="modalStartDate">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Trip End</span>
                        <span class="detail-value" id="modalEndDate">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Rental Days</span>
                        <span class="detail-value" id="modalDays">-</span>
                    </div>
                </div>
            </div>

            <!-- Pricing Details -->
            <div class="modal-section pricing-section">
                <h3 class="section-title">Pricing</h3>
                <div class="pricing-breakdown">
                    <div class="pricing-row">
                        <span class="pricing-label">Total Price</span>
                        <span class="pricing-value">₱<span id="modalTotal">0.00</span></span>
                    </div>
                    <div class="pricing-row">
                        <span class="pricing-label">Downpayment (30%)</span>
                        <span class="pricing-value">₱<span id="modalDownpayment">0.00</span></span>
                    </div>
                </div>
            </div>

            <!-- Customer Name (if logged in) -->
            @if(Auth::check())
                <div class="modal-section">
                    <h3 class="section-title">Customer Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Name</span>
                        <span class="detail-value">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            @else
                <div class="modal-section">
                    <h3 class="section-title">Customer Information</h3>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" id="customerName" class="form-input" placeholder="Enter your name" required>
                    </div>
                </div>
            @endif
        </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeBookingModal()">Cancel</button>
                <button type="submit" class="btn btn-confirm">Confirm Booking</button>
            </div>
        </form>
    </div>
</div>
