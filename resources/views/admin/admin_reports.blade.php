@extends('layouts.admin_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_css/admin_reports.css') }}">
@endsection

@section('content')
<div class="reports-page">

    {{-- Page Header --}}
    <div class="page-header">
        <h1>Reports</h1>
    </div>

    {{-- ===== FILTER SECTION ===== --}}
    <div class="filter-card">
        <div class="section-label">Report filters</div>
        <form method="GET" action="{{ route('admin.reports') }}" class="filter-grid">
            @csrf

            {{-- Date Range --}}
            <div class="field">
                <label for="date_from">Date range</label>
                <div class="date-row">
                    <input type="date" id="date_from" name="date_from"
                           value="{{ request('date_from', now()->startOfWeek()->format('Y-m-d')) }}">
                    <input type="date" id="date_to" name="date_to"
                           value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>
            </div>

            {{-- Report Type --}}
            <div class="field">
                <label for="report_type">Report type</label>
                <select id="report_type" name="report_type">
                    <option value="rentals" {{ request('report_type','rentals') == 'rentals' ? 'selected' : '' }}>Rentals</option>
                    <option value="revenue" {{ request('report_type') == 'revenue'            ? 'selected' : '' }}>Revenue</option>
                    <option value="users"   {{ request('report_type') == 'users'              ? 'selected' : '' }}>Customer</option>
                </select>
            </div>

            {{-- Generate Button --}}
            <div class="field">
                <label>&nbsp;</label>
                <button type="submit" class="btn-generate">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="8" cy="8" r="6"/>
                        <path d="M8 5v3l2 2"/>
                    </svg>
                    Generate report
                </button>
            </div>
        </form>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#7c3aed" stroke-width="1.8">
                    <path d="M2 11l4-4 3 3 5-6"/>
                </svg>
            </div>
            <div class="stat-label">Total revenue</div>
            <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stat-badge up">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4"/></svg>
                {{ $stats['revenue_growth'] }}% from last week
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#059669" stroke-width="1.8">
                    <rect x="2" y="5" width="12" height="9" rx="1"/>
                    <path d="M5 5V3.5a3 3 0 016 0V5"/>
                </svg>
            </div>
            <div class="stat-label">Total rentals</div>
            <div class="stat-value">{{ number_format($stats['total_rentals']) }}</div>
            <div class="stat-badge up">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4"/></svg>
                {{ $stats['rentals_growth'] }}% from last week
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#2563eb" stroke-width="1.8">
                    <circle cx="6" cy="5" r="2.5"/>
                    <path d="M1.5 13c0-2.5 2-4 4.5-4s4.5 1.5 4.5 4"/>
                    <circle cx="12" cy="5" r="2"/>
                    <path d="M14.5 13c0-2-1.5-3-3-3"/>
                </svg>
            </div>
            <div class="stat-label">Total customers</div>
            <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
            <div class="stat-badge up">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4"/></svg>
                {{ $stats['customers_growth'] }}% from last week
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#d97706" stroke-width="1.8">
                    <rect x="2" y="3" width="12" height="10" rx="1"/>
                    <path d="M2 7h12M6 3v2M10 3v2"/>
                </svg>
            </div>
            <div class="stat-label">Total transactions</div>
            <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
            <div class="stat-badge up">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4"/></svg>
                {{ $stats['transactions_growth'] }}% from last week
            </div>
        </div>
    </div>

    {{-- ===== MAIN TABLE CARD ===== --}}
    <div class="main-card" data-report="
    @if(request('report_type') == 'revenue') Revenue
    @elseif(request('report_type') == 'users') Customer
    @else Rentals
    @endif
    ">
        <div class="table-header">
            <div class="table-header-left">
                <span>
                    @if(request('report_type') == 'revenue') Revenue report
                    @elseif(request('report_type') == 'users') Customer report
                    @else Rentals report
                    @endif
                </span>
                <span class="badge-count">{{ $records->total() }} records</span>
            </div>
            <button onclick="printReport()" class="btn-export pdf">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M2 1h6l2 2v8H2V1z"/><path d="M7 1v3h3"/><path d="M4 7h4M4 9h2"/>
                </svg>
                Export PDF
            </button>
        </div>

       

        {{-- ===== RENTALS TABLE ===== --}}
        @if(request('report_type', 'rentals') == 'rentals')
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer name</th>
                        <th>Vehicle</th>
                        <th>Plate No</th>
                        <th>Rental date</th>
                        <th>Return date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $rental)
                    <tr>
                        <td class="muted">{{ str_pad($records->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $rental->customer->name ?? '—' }}</td>
                        <td>
                            <div class="car-cell">
                                {{-- @if($rental->vehicle->image ?? false)
                                    <img src="{{ asset('assets/images/images-vehicles/' . $rental->vehicle->image) }}" class="car-thumb-img" alt="">
                                @else
                                    <div class="car-thumb">🚗</div>
                                @endif --}}
                                  {{ ($rental->vehicle->brand ?? '') . ' ' . ($rental->vehicle->model ?? '—') }}
                            </div>
                        </td>
                        <td class="muted">{{ $rental->vehicle->plate_no ?? '—' }}</td> 
                        <td>{{ \Carbon\Carbon::parse($rental->rent_start)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($rental->rent_end)->format('M d, Y') }}</td>
                        <td class="amount">₱{{ number_format($rental->total, 2) }}</td>
                        <td>
                            <span class="status {{ strtolower($rental->status) }}">
                                {{ ucfirst($rental->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">No rental records found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== REVENUE TABLE ===== --}}
        @elseif(request('report_type') == 'revenue')
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Plate No</th>
                        <th>Date paid</th>
                        <th>Amount</th>
                        <th>Payment method</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $transaction)
                    <tr>
                        <td class="muted">#{{ str_pad($transaction->payment_ID, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $transaction->customer->name ?? '—' }}</td>
                        <td>{{ ($transaction->booking->vehicle->brand ?? '') . ' ' . ($transaction->booking->vehicle->model ?? '—') }}</td>
                        <td class="muted">{{ $transaction->booking->vehicle->plate_no ?? '—' }}</td> 
                        <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('M d, Y') }}</td>
                        <td class="amount">₱{{ number_format($transaction->amount_paid, 2) }}</td>
                        <td>{{ $transaction->payment_method }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">No revenue records found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== USERS TABLE ===== --}}
        @elseif(request('report_type') == 'users')
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date registered</th>
                        <th>Total rentals</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $user)
                    <tr>
                        <td class="muted">{{ str_pad($records->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $user->name }}</td>
                        <td class="muted">{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</td>
                        <td>{{ $user->rentals_count ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">No user records found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- Pagination --}}
        <div class="pagination-wrap">
            <span class="page-info">
                Showing {{ $records->firstItem() }}–{{ $records->lastItem() }} of {{ number_format($records->total()) }} records
            </span>
            <div class="pagination-links">
                {{ $records->appends(request()->all())->links('pagination::simple-bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

<div id="report_type_data"
    data-type="{{ request('report_type', 'rentals') }}"
    data-from="{{ request('date_from', now()->startOfWeek()->format('Y-m-d')) }}"
    data-to="{{ request('date_to', now()->format('Y-m-d')) }}"
    style="display:none;">
</div>


@endsection

@section('scripts')
  <script src="{{ asset('javascripts/admin_js/admin_report.js') }}"></script>
@endsection