<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VionAuto - Car Rental Service</title>
    @vite(['resources/css/landing.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    @include('layouts.landing_page_navbar')

    <!-- Hero Section with Search -->
    <section class="hero">
        <!-- Background Image Placeholder -->
        <div class="hero-bg"><img src="{{asset('assets/images/landing-page-bg-img.png')}}" alt=""></div>

        <div class="hero-content">
            <h1 class="hero-title">Find Your Perfect Ride</h1>
            <p class="hero-subtitle">Book a car easily and enjoy your journey</p>

            <!-- Search Bar -->
            <div class="search-bar">
                <div class="search-field">
                    <div class="search-label">
                        <span class="search-icon"><img src="{{asset('assets/icons/category.png')}}" alt=""></span>
                        <label>Category</label>
                    </div>
                    <select id="categorySelect" class="search-input search-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="search-field">
                    <div class="search-label">
                        <span class="search-icon"><img src="{{asset('assets/icons/brand.png')}}" alt=""></span>
                        <label>Brand</label>
                    </div>
                    <input type="text" id="brandInput" placeholder="Enter brand" class="search-input">
                </div>
                <div class="search-field">
                    <div class="search-label">
                        <span class="search-icon"><img src="{{asset('assets/icons/dollar.png')}}" alt=""></span>
                        <label>Price Range</label>
                    </div>
                    <select id="priceSelect" class="search-input search-select">
                        <option value="">All Prices</option>
                        <option value="0-500">₱0 - ₱500</option>
                        <option value="500-1000">₱500 - ₱1,000</option>
                        <option value="1000-1500">₱1,000 - ₱1,500</option>
                        <option value="1500-2000">₱1,500 - ₱2,000</option>
                        <option value="2000-3000">₱2,000 - ₱3,000</option>
                        <option value="3000-5000">₱3,000 - ₱5,000</option>
                        <option value="5000">₱5,000+</option>
                    </select>
                </div>
                <button class="search-btn">Search Car</button>
            </div>
        </div>
    </section>

    <!-- Discover Cars Section -->
    <section class="discover-section" id="cars">
        <div class="discover-header">
            <div>
                <h2 class="section-title">Discover Cars</h2>
                <p class="section-subtitle">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempore incididunt ut labore et dolore magna aliqua.</p>
            </div>
            <a href="{{ route('customer.browse-all-vehicles') }}" class="browse-all-btn">Browse All</a>
        </div>

        <!-- Vehicle Carousel -->
        <div class="carousel-container">
            <button class="carousel-btn carousel-prev" id="prevBtn" aria-label="Previous vehicles">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <div class="carousel-wrapper">
                <div class="carousel-track" id="carouselTrack">
                    @forelse($vehicles as $vehicle)
                        <x-vehicle-card :vehicle="$vehicle" />
                    @empty
                        <div class="empty-state">
                            <p>No vehicles available at the moment</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <button class="carousel-btn carousel-next" id="nextBtn" aria-label="Next vehicles">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <!-- Carousel Indicators -->
        <div class="carousel-indicators" id="carouselIndicators"></div>
    </section>

    <!-- About Us Section -->
    <section class="about-section" id="about">
        <div class="about-container">
            <div class="about-content">
                <h2 class="section-title">About Us</h2>
                <p class="about-text">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempore incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.
                </p>
                <p class="about-text">
                    Is a duis lorem dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
            </div>
            
            <!-- About Image Placeholder -->
            <div class="about-image-placeholder">
                <img src="{{asset('assets/images/images-vehicles/sample_suv.png')}}" alt="sample_suv">
            </div>
        </div>
    </section>

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
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach((input, index) => {
            const placeholderText = index === 0 ? 'Trip Start' : 'Trip End';
            
            // Show placeholder on load
            if (!input.value) {
                input.style.color = 'transparent';
            }
            
            // Show placeholder when empty
            input.addEventListener('change', () => {
                input.style.color = input.value ? '#333' : 'transparent';
            });
            
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.style.color = 'transparent';
                }
            });
            
            input.addEventListener('focus', () => {
                input.style.color = '#333';
            });
        });

        // Carousel functionality
        const carouselTrack = document.getElementById('carouselTrack');
        const carouselItems = document.querySelectorAll('.carousel-item');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const indicatorsContainer = document.getElementById('carouselIndicators');

        let currentIndex = 0;
        const itemsPerView = 3;
        const itemCount = carouselItems.length;

        // Create indicators
        function createIndicators() {
            const totalSlides = Math.ceil(itemCount / itemsPerView);
            
            for (let i = 0; i < totalSlides; i++) {
                const indicator = document.createElement('button');
                indicator.className = 'carousel-indicator' + (i === 0 ? ' active' : '');
                indicator.setAttribute('aria-label', `Go to slide ${i + 1}`);
                
                indicator.addEventListener('click', () => {
                    currentIndex = i * itemsPerView;
                    updateCarousel();
                });
                
                indicatorsContainer.appendChild(indicator);
            }
        }

        // Update carousel position
        function updateCarousel() {
            const offset = -(currentIndex * (100 / itemsPerView));
            carouselTrack.style.transform = `translateX(${offset}%)`;
            
            // Update indicators
            const totalSlides = Math.ceil(itemCount / itemsPerView);
            const activeSlide = Math.floor(currentIndex / itemsPerView);
            
            document.querySelectorAll('.carousel-indicator').forEach((indicator, index) => {
                indicator.classList.toggle('active', index === activeSlide);
            });

            // Update button states
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= itemCount - itemsPerView;
        }

        // Navigation handlers
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex -= itemsPerView;
                updateCarousel();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < itemCount - itemsPerView) {
                currentIndex += itemsPerView;
                updateCarousel();
            }
        });

        // Initialize
        createIndicators();
        updateCarousel();

        // Search button handler
        const searchBtn = document.querySelector('.search-btn');
        searchBtn.addEventListener('click', () => {
            const category = document.getElementById('categorySelect').value;
            const brand = document.getElementById('brandInput').value;
            const price = document.getElementById('priceSelect').value;

            // Build URL with query parameters
            const params = new URLSearchParams();
            if (category) params.append('category', category);
            if (brand) params.append('brand', brand);
            if (price) params.append('price', price);

            const browseUrl = "{{ route('customer.browse-all-vehicles') }}" + (params.toString() ? '?' + params.toString() : '');
            window.location.href = browseUrl;
        });
    </script>
