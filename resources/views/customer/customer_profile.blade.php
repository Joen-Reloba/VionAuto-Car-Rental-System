<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Profile</title>
  @vite(['resources/css/customer_css/customer_profile.css'])
    
</head>
<body>

    @include('layouts.landing_page_navbar')

    <div class="profile-page">

        {{-- ── TOP BREADCRUMB BAR ── --}}
        <div class="profile-topbar">
            <div class="profile-topbar-inner">
                <span class="breadcrumb">
                    <a href="{{ route('landing') }}">Home</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">My Profile</span>
                </span>
                <button class="btn-reset-password" onclick="openPasswordModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Reset Password
                </button>
            </div>
        </div>

        <div class="profile-content">

            {{-- ── ALERTS ── --}}
            @if(session('success'))
                <div class="alert alert-success">&#10003; {{ session('success') }}</div>
            @endif

            {{-- ── PROFILE HEADER ── --}}
           <div class="profile-header-card">
    <div class="profile-avatar">
        <img src="{{ asset('assets/icons/customer.png') }}" alt="">
    </div>
                <div class="profile-header-info">
                    <h1 class="profile-name">
                        {{ trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name) }}
                        <span class="status-badge">&#8226; Active</span>
                    </h1>
                    <p class="profile-sub">
                        Member since {{ $user->created_at->format('F Y') }}
                        &nbsp;&middot;&nbsp; {{ $user->email }}
                        &nbsp;&middot;&nbsp; {{ $user->username }}
                    </p>
                </div>
                <!-- Add this edit button -->
                <button class="btn-edit-profile" onclick="openEditModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit Profile
                </button>
            </div>

            {{-- ── INFO + ID GRID ── --}}
            <div class="info-grid">

                {{-- Personal Information --}}
                <div class="info-card">
                    <p class="card-label">Personal Information</p>
                    <div class="info-row">
                        <span class="info-key">Full name</span>
                        <span class="info-val">{{ trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Date of birth</span>
                        <span class="info-val">{{ $customer->birthday ? \Carbon\Carbon::parse($customer->birthday)->format('F d, Y') : '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Address</span>
                        <span class="info-val">{{ $customer->address ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Username</span>
                        <span class="info-val">{{ $user->username }}</span>
                    </div>
                    <div class="info-row no-border">
                        <span class="info-key">Email</span>
                        <span class="info-val">{{ $user->email }}</span>
                    </div>
                </div>

                {{-- License & Valid ID --}}
                <div class="info-card">
                    <p class="card-label">License &amp; Valid ID</p>

                    {{-- Driver's License — text only --}}
                    <div class="license-block">
                        <div class="license-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <circle cx="8" cy="11" r="2"/>
                                <line x1="13" y1="10" x2="20" y2="10"/>
                                <line x1="13" y1="13" x2="17" y2="13"/>
                            </svg>
                        </div>
                        <div class="license-info">
                            <strong>Driver's License</strong>
                            <p>No. {{ $customer->license_no ?? '—' }}</p>
                            <p>Expires: {{ $customer->license_expiry ? \Carbon\Carbon::parse($customer->license_expiry)->format('M d, Y') : '—' }}</p>
                        </div>
                    </div>

                    <div class="id-divider"></div>

                    {{-- Valid ID — image --}}
                    <p class="id-sublabel">Valid Government ID</p>
                    @if($customer->valid_ID)
                        <div class="valid-id-wrapper" onclick="openIdViewer('{{ asset('assets/images/images-valid_id/' . $customer->valid_ID) }}')">
                            <img
                                src="{{ asset('assets/images/images-valid_id/' . $customer->valid_ID) }}"
                                alt="Valid ID"
                                class="valid-id-thumb"
                            />
                            <div class="valid-id-overlay">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" width="20" height="20">
                                    <circle cx="11" cy="11" r="8"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    <line x1="11" y1="8" x2="11" y2="14"/>
                                    <line x1="8" y1="11" x2="14" y2="11"/>
                                </svg>
                                <span>View Full Image</span>
                            </div>
                        </div>
                    @else
                        <div class="valid-id-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <span>No ID uploaded</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── STATS ROW ── --}}
            <div class="stats-row">
                <div class="stat-card">
                    <p class="stat-label">Total Bookings</p>
                    <p class="stat-val">{{ $totalBookings }}</p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Completed</p>
                    <p class="stat-val green">{{ $completed }}</p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Ongoing</p>
                    <p class="stat-val amber">{{ $ongoing }}</p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Total Spent</p>
                    <p class="stat-val purple">&#8369;{{ number_format($totalSpent, 2) }}</p>
                </div>
            </div>

            {{-- ── BOOKING HISTORY TABLE ── --}}
            <div class="table-card">
                <div class="table-topbar">
                    <h2 class="table-title">Booking History</h2>
                    <input type="text" class="table-search" placeholder="Search bookings..." onkeyup="filterBookingTable(this.value)" />
                </div>

                <div class="table-wrapper">
                    <table id="bookingTable">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Car</th>
                                <th>Start Rental</th>
                                <th>End Rental</th>
                                <th>Duration</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->bookings->sortByDesc('created_at') as $booking)
                            <tr>
                                <td class="booking-id">#{{ str_pad($booking->booking_ID, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="car-cell">
                                    <span class="car-dot"></span>
                                    {{ ($booking->vehicle->brand ?? '') . ' ' . ($booking->vehicle->model ?? 'N/A') }}
                                </td>
                                <td>{{ $booking->rent_start ? $booking->rent_start->format('M d, Y') : '—' }}</td>
                                <td>{{ $booking->rent_end ? $booking->rent_end->format('M d, Y') : '—' }}</td>
                                <td>
                                    @if($booking->rent_start && $booking->rent_end)
                                        {{ $booking->rent_start->diffInDays($booking->rent_end) }} day(s)
                                    @else —
                                    @endif
                                </td>
                                <td class="amount-cell">&#8369;{{ number_format($booking->total ?? 0, 2) }}</td>
                                <td>
                                    @php $status = strtolower($booking->status ?? 'pending'); @endphp
                                    <span class="status-chip chip-{{ $status }}">{{ ucfirst($status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-row">No bookings yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <span class="table-count">{{ $customer->bookings->count() }} booking(s) total</span>
                </div>
            </div>

        </div>{{-- /profile-content --}}
    </div>{{-- /profile-page --}}

    {{-- ── VALID ID LIGHTBOX ── --}}
    <div class="lightbox-overlay" id="idLightbox" onclick="closeIdViewer()">
        <div class="lightbox-box" onclick="event.stopPropagation()">
            <button class="lightbox-close" onclick="closeIdViewer()">&times;</button>
            <img id="lightboxImg" src="" alt="Valid ID" />
        </div>
    </div>

    {{-- ── RESET PASSWORD MODAL ── --}}
    <div class="modal-overlay" id="passwordModal" onclick="closePasswordModal()">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>Reset Password</h3>
                <button class="modal-close" onclick="closePasswordModal()">&times;</button>
            </div>

            @if($errors->has('current_password'))
                <div class="alert alert-error" style="margin-bottom:1rem">
                    &#9888; {{ $errors->first('current_password') }}
                </div>
            @endif

            <form action="{{ route('customer.profile.password') }}" method="POST" class="password-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-wrap">
                        <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required />
                        <button type="button" class="eye-btn" onclick="togglePw('current_password', this)" tabindex="-1">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required />
                        <button type="button" class="eye-btn" onclick="togglePw('password', this)" tabindex="-1">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password" required />
                        <button type="button" class="eye-btn" onclick="togglePw('password_confirmation', this)" tabindex="-1">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closePasswordModal()">Cancel</button>
                    <button type="submit" class="btn-save">Update Password</button>
                </div>
            </form>
        </div>
    </div>

     @include('customer.customer_update_profile')



    <div id="appData"
        data-open-edit="{{ ($errors->any() && old('first_name')) ? 'true' : 'false' }}"
        data-open-password="{{ ($errors->has('current_password') || session('open_password_modal')) ? 'true' : 'false' }}"
        style="display:none;">
    </div>

     <script src="{{ asset('javascripts/customer_js/customer_profile.js') }}"></script>
</body>
</html>