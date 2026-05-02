<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse All Vehicles - VionAuto</title>
    {{-- @vite(['resources/css/customer_css/browse_all_vehicles.css','resources/css/landing.css']) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    @include('layouts.landing_page_navbar')

    <!-- Browse All Vehicles Page -->
    <div class="browse-container">
        <!-- Header with Back Button -->
        <div class="browse-header">
            <a href="{{ route('landing') }}" class="back-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                <span>Back</span>
            </a>
            <h1 class="browse-title">All Vehicles</h1>
            <div class="header-spacer"></div>
        </div>

        <div class="browse-content">
            <!-- Filters Section -->
            <aside class="filters-section">
                <div class="filters-header">
                    <h2>Filter</h2>
                    <a href="{{ route('customer.browse-all-vehicles') }}" class="reset-filters">Reset</a>
                </div>

                <form action="{{ route('customer.browse-all-vehicles') }}" method="GET" id="filterForm">
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <label for="filterCategory" class="filter-label">Category</label>
                        <select id="filterCategory" name="category" class="filter-input">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand Filter -->
                    <div class="filter-group">
                        <label for="filterBrand" class="filter-label">Brand</label>
                        <input 
                            type="text" 
                            id="filterBrand" 
                            name="brand" 
                            placeholder="Enter brand" 
                            class="filter-input"
                            value="{{ $selectedBrand }}"
                        >
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <label for="filterPrice" class="filter-label">Price Range</label>
                        <select id="filterPrice" name="price" class="filter-input">
                            <option value="">All Prices</option>
                            <option value="0-500" {{ $selectedPrice === '0-500' ? 'selected' : '' }}>₱0 - ₱500</option>
                            <option value="500-1000" {{ $selectedPrice === '500-1000' ? 'selected' : '' }}>₱500 - ₱1,000</option>
                            <option value="1000-1500" {{ $selectedPrice === '1000-1500' ? 'selected' : '' }}>₱1,000 - ₱1,500</option>
                            <option value="1500-2000" {{ $selectedPrice === '1500-2000' ? 'selected' : '' }}>₱1,500 - ₱2,000</option>
                            <option value="2000-3000" {{ $selectedPrice === '2000-3000' ? 'selected' : '' }}>₱2,000 - ₱3,000</option>
                            <option value="3000-5000" {{ $selectedPrice === '3000-5000' ? 'selected' : '' }}>₱3,000 - ₱5,000</option>
                            <option value="5000" {{ $selectedPrice === '5000' ? 'selected' : '' }}>₱5,000+</option>
                        </select>
                    </div>

                    <button type="submit" class="apply-filters-btn">Apply Filters</button>
                </form>
            </aside>

            <!-- Vehicles Grid Section -->
            <main class="vehicles-section">
                @if($vehicles->count() > 0)
                    <div class="vehicles-grid">
                        @foreach($vehicles as $vehicle)
                            <x-vehicle-card :vehicle="$vehicle" />
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6m0 4v.01"></path>
                        </svg>
                        <h3>No Vehicles Found</h3>
                        <p>Try adjusting your filters to find available vehicles.</p>
                        <a href="{{ route('customer.browse-all-vehicles') }}" class="reset-link">View All Vehicles</a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <!-- Left Section -->
            <div class="footer-left">
                <h3 class="footer-brand">VionAuto</h3>
                <p class="footer-tagline">Driven by Comfort, Powered by Trust.</p>
                <p class="footer-location">Toril, Davao City, Philippines</p>
                <p class="footer-contact">Contact us @ <a href="mailto:vionauto2026@gmail.com">vionauto2026@gmail.com</a></p>
            </div>

            <!-- Right Section -->
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

        <!-- Bottom Section -->
        <div class="footer-bottom">
            <p>&copy; 2026 VionAuto. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Auto-submit form on filter change
        const filterForm = document.getElementById('filterForm');
        const filterInputs = filterForm.querySelectorAll('select, input[type="text"]');

        filterInputs.forEach(input => {
            input.addEventListener('change', () => {
                filterForm.submit();
            });
        });
    </script>
</body>
</html>
