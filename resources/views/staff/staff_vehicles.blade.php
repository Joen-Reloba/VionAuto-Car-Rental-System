@extends('layouts.staff_layout')

@section('styles')
    @vite(['resources/css/staff_css/staff_vehicles.css'])
@endsection

@section('content')
<div class="cars-page">

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <h1 class="page-title">Manage Vehicles</h1>
        
    </div>

    {{-- ── Toolbar: Search + Filters ── --}}
    <div class="toolbar">
        <div class="search-wrap">
            <input type="text" id="searchInput" class="search-input" placeholder="Search brand, model…">
            <button class="search-btn" onclick="filterCards()">Search</button>
        </div>
        <div class="filters">
            <select id="statusFilter" class="filter-select" onchange="filterCards()">
                <option value="">All Statuses</option>
                <option value="available">Available</option>
                <option value="booked">Booked</option>
                <option value="rented">Rented</option>
                <option value="maintenance">Maintenance</option>
                <option value="unavailable">Unavailable</option>
            </select>
            <select id="typeFilter" class="filter-select" onchange="filterCards()">
                <option value="">All Types</option>
                <option value="sedan">Sedan</option>
                <option value="suv">SUV</option>
                <option value="van">Van</option>
                <option value="pickup">Pickup Truck</option>
            </select>

             <button class="add-btn" id="addVehicleBtn">+ Add Vehicle</button>
        </div>
    </div>

    {{-- ── Results Count ── --}}
    <p class="results-bar" id="resultsBar"></p>

    {{-- ── Card Grid ── --}}
    <div class="car-grid" id="carGrid"></div>

    {{-- ── Pagination ── --}}
    @if($cars->hasPages())
        <div class="pagination-wrapper">
            {{ $cars->links('vendor.pagination.custom') }}
        </div>
    @endif

</div>

{{-- ── Vehicle Detail Modal ── --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-card">
        <button class="modal-close" id="modalClose">&times;</button>

        <div class="modal-img-wrap">
            <img id="modalImage" src="" alt="" class="modal-img hidden">
            <div id="modalNoImg" class="modal-no-img">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="7" width="20" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5 7l2-3h10l2 3" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="17" cy="18" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
        </div>

        <div class="modal-body">
            <div class="modal-header-row">
                <div>
                    <h2 class="modal-car-name" id="modalCarName"></h2>
                    <span class="modal-type-badge" id="modalTypeBadge"></span>
                </div>
                <span class="status-pill" id="modalStatusPill"></span>
            </div>

            <div class="modal-details-grid">
                <div class="detail-item">
                    <span class="detail-label">Brand</span>
                    <span class="detail-value" id="modalBrand"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Model</span>
                    <span class="detail-value" id="modalModel"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Daily Rate</span>
                    <span class="detail-value modal-price" id="modalPrice"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Type</span>
                    <span class="detail-value" id="modalType"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date Added</span>
                    <span class="detail-value" id="modalDate"></span>
                </div>

                <div class="detail-item" id="modalDescriptionWrap" style="grid-column: 1 / -1;">
                    <span class="detail-label">Description</span>
                    <span class="detail-value" id="modalDescription" style="white-space: pre-wrap;"></span>
                </div>
            </div>

            <div class="modal-actions">
                <button class="modal-edit-btn" id="modalEditBtn">Update Vehicle</button>
                <button class="modal-delete-btn" id="modalDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Pass Laravel data to JS ── --}}
<script>
    const carsData = @json($carsData);
    console.log('carsData:', carsData);  // DEBUG: Check what data we have
    console.log('First car image:', carsData[0]?.image);  // Check first image path

    const routes = {
        delete: "{{ route('staff.vehicles.destroy', ':id') }}",
        edit:   "{{ route('staff.vehicles.edit', ':id') }}",
    };

    const csrfToken = "{{ csrf_token() }}";
</script>

<script src="{{ asset('javascripts/staff_js/staff_vehicle.js') }}"></script>
@endsection
