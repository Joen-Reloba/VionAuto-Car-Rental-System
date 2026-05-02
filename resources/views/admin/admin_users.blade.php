@extends('layouts.admin_layout')

@section('styles')
    @vite('resources/css/admin_css/admin_users.css')
@endsection

@section('content')

    <div class="page-header">
        <h1 class="page-title">Manage Users</h1>
    </div>

    <div class="table-card">

        <div class="table-topbar">
            <div class="search-wrap">
                <input type="text" class="search-input" id="searchInput" placeholder="Search by name or ID...">
            </div>
            <div class="topbar-right">
                <div class="filter-wrap">
                    <select class="role-filter" id="roleFilter">
                        <option value="all">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <button class="add-btn" id="addUserBtn">+ Add User</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="user-table" id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr data-role="{{ $user->role }}"
                            data-search="{{ strtolower(trim($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name)) }} {{ $user->user_ID }}"
                            data-user-id="{{ $user->user_ID }}"
                            data-user-type="user">
                            <td>{{ $user->user_ID }}</td>
                            <td>{{ trim($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name) }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="role-pill role-{{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span></td>
                            <td><span class="status-pill {{ $user->status }}">{{ $user->status === 'active' ? '✔ Active' : 'Inactive' }}</span></td>
                            <td class="action-cell">
                               <button class="action-btn view-btn" title="View" data-id="{{ $user->user_ID }}">
                                    <img src="{{ asset('assets/icons/view.png') }}" alt="View">
                                </button>
                                @if($user->role !== 'customer')
                                    <button class="action-btn edit-btn" title="Edit" data-id="{{ $user->user_ID }}">
                                        <img src="{{ asset('assets/icons/update.png') }}" alt="Edit">
                                    </button>
                                @endif
                                <button class="action-btn delete-btn" title="Delete" data-id="{{ $user->user_ID }}">
                                    <img src="{{ asset('assets/icons/delete.png') }}" alt="Delete">
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-msg" style="text-align: center; padding: 40px 0; color: #999;">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Edit User Modal --}}
    @include('admin.admin_update_user')

    {{-- Add User Modal --}}
    @include('admin.admin_add_user')

    {{-- User Detail Modal --}}
    <div id="userModal" class="modal modal-hidden">
        <div class="modal-content">
            <button class="modal-close" onclick="closeUserModal()">&times;</button>
            
            <h2 class="modal-title">User Details</h2>
            
            <div class="modal-body">
                <div class="detail-section">
                    <h3>Personal Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>User ID</label>
                            <p id="modalUserId">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Name</label>
                            <p id="modalName">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <p id="modalPhone">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <p id="modalEmail">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Username</label>
                            <p id="modalUsername">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Role</label>
                            <p id="modalRole">-</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section" id="staffSection" style="display: none;">
                    <h3>Staff Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Employee Number</label>
                            <p id="modalEmployeeNo">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Position</label>
                            <p id="modalPosition">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Hired Date</label>
                            <p id="modalHiredAt">-</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section" id="customerSection" style="display: none;">
                    <h3>Customer Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Birthday</label>
                            <p id="modalBirthday">-</p>
                        </div>
                        <div class="detail-item">
                            <label>Address</label>
                            <p id="modalAddress">-</p>
                        </div>
                        <div class="detail-item">
                            <label>License Number</label>
                            <p id="modalLicenseNo">-</p>
                        </div>
                        <div class="detail-item">
                            <label>License Expiry</label>
                            <p id="modalLicenseExpiry">-</p>
                        </div>
                        <div class="detail-item full-width">
                            <label>Valid ID</label>
                            <img id="modalValidId" src="" alt="Valid ID" style="max-width: 100%; height: auto; max-height: 300px; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Account Status</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Status</label>
                            <p id="modalStatus">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteConfirmModal" class="modal modal-hidden">
        <div class="modal-content">
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            
            <h2 class="modal-title">Confirm Delete</h2>
            
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteUserName">this user</strong>?</p>
                <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
                    ⚠️ This action cannot be undone. If this user has active bookings or payments, deletion will be prevented.
                </p>
            </div>

            <div class="modal-footer">
                <button id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
                <button id="confirmDeleteBtn" class="btn btn-danger">Delete User</button>
            </div>
        </div>
    </div>

   @section('scripts')
    <script src="{{ asset('javascripts/admin_js/admin_user.js') }}"></script>
    @endsection

@endsection

