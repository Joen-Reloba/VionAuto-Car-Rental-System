@props(['vehicle'])

<div class="carousel-item">
    <div class="vehicle-card">
        <div class="vehicle-image-wrapper">
            @if($vehicle['image'])
                <img src="{{ $vehicle['image'] }}" alt="{{ $vehicle['brand'] }} {{ $vehicle['model'] }}" class="vehicle-image">
            @else
                <div class="no-image-placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
            @endif
        </div>

        <div class="vehicle-info">
            <div class="vehicle-price">
                <span class="price-amount">₱{{ number_format($vehicle['daily_rate'], 2) }}</span>
                <span class="price-label">per day</span>
            </div>

            <h3 class="vehicle-name">{{ $vehicle['brand'] }} {{ $vehicle['model'] }}</h3>
            <p class="vehicle-category">{{ $vehicle['category'] }}</p>

            <a href="{{ route('customer.view-vehicle', $vehicle['vehicle_ID']) }}" class="vehicle-browse-btn">Browse</a>
        </div>
    </div>
</div>
