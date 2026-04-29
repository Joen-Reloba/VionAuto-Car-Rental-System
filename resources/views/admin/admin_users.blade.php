@extends('layouts.admin_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_css/admin_users.css') }}">
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
                                <button class="action-btn view-btn" title="View" onclick="viewUser('{{ $user->user_ID }}')">
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

    <script>
        function viewUser(userId) {
            fetch(`/admin/users/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const user = data.user;
                    const staff = data.staff;
                    const customer = data.customer;

                    // Basic info
                    document.getElementById('modalUserId').textContent = user.user_ID;
                    document.getElementById('modalName').textContent = data.full_name;
                    document.getElementById('modalPhone').textContent = user.phone_number || '-';
                    document.getElementById('modalEmail').textContent = user.email || '-';
                    document.getElementById('modalUsername').textContent = user.username || '-';
                    document.getElementById('modalRole').textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);
                    document.getElementById('modalStatus').textContent = user.status.charAt(0).toUpperCase() + user.status.slice(1);

                    // Hide/show staff section
                    const staffSection = document.getElementById('staffSection');
                    const customerSection = document.getElementById('customerSection');
                    
                    if (user.role === 'staff' && staff) {
                        staffSection.style.display = 'block';
                        document.getElementById('modalEmployeeNo').textContent = staff.employee_no || '-';
                        document.getElementById('modalPosition').textContent = staff.position || '-';
                        document.getElementById('modalHiredAt').textContent = staff.hired_at ? new Date(staff.hired_at).toLocaleDateString() : '-';
                        customerSection.style.display = 'none';
                    } else if (user.role === 'customer' && customer) {
                        customerSection.style.display = 'block';
                        document.getElementById('modalBirthday').textContent = customer.birthday || '-';
                        document.getElementById('modalAddress').textContent = customer.address || '-';
                        document.getElementById('modalLicenseNo').textContent = customer.license_no || '-';
                        document.getElementById('modalLicenseExpiry').textContent = customer.license_expiry || '-';
                        const validIdImg = document.getElementById('modalValidId');
                        if (customer.valid_ID) {
                            validIdImg.src = '/assets/images/valid-ids/' + customer.valid_ID;
                            validIdImg.style.display = 'block';
                        } else {
                            validIdImg.src = '';
                            validIdImg.style.display = 'none';
                        }
                        staffSection.style.display = 'none';
                    } else {
                        staffSection.style.display = 'none';
                        customerSection.style.display = 'none';
                    }

                    const modal = document.getElementById('userModal');
                    modal.classList.remove('modal-hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading user details');
                });
        }

        function closeUserModal() {
            const modal = document.getElementById('userModal');
            modal.classList.add('modal-hidden');
        }

        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUserModal();
            }
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.user-table tbody tr');
            
            rows.forEach(row => {
                if (row.textContent.toLowerCase().includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Edit button functionality
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                editUser(userId);
            });
        });

        function editUser(userId) {
            fetch(`/admin/users/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const user = data.user;
                    
                    // Populate form fields
                    document.getElementById('editUserId').value = user.user_ID;
                    document.getElementById('editFirstName').value = user.first_name || '';
                    document.getElementById('editMiddleName').value = user.middle_name || '';
                    document.getElementById('editLastName').value = user.last_name || '';
                    document.getElementById('editUsername').value = user.username || '';
                    document.getElementById('editEmail').value = user.email || '';
                    document.getElementById('editPhone').value = user.phone_number || '';
                    document.getElementById('editPassword').value = '';
                    document.getElementById('editRole').value = user.role || '';
                    document.getElementById('editStatus').value = user.status || '';
                    
                    const modal = document.getElementById('editUserModal');
                    modal.classList.remove('modal-hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading user details');
                });
        }

        function closeEditModal() {
            const modal = document.getElementById('editUserModal');
            modal.classList.add('modal-hidden');
        }

        // Handle edit form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = document.getElementById('editUserId').value;
            const form = document.getElementById('editUserForm');
            const csrfToken = form.querySelector('input[name="_token"]').value;
            
            const formData = new FormData(form);
            
            fetch(`/admin/users/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User updated successfully');
                    closeEditModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update user'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating user: ' + error.message);
            });
        });

        // Close modal when clicking outside
        document.getElementById('editUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Role filter
        document.getElementById('roleFilter').addEventListener('change', function(e) {
            const selectedRole = e.target.value;
            const rows = document.querySelectorAll('.user-table tbody tr');
            
            rows.forEach(row => {
                const rowRole = row.getAttribute('data-role');
                if (selectedRole === 'all' || rowRole === selectedRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Delete button functionality
        let currentDeleteUserId = null;

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const userRow = this.closest('tr');
                const userName = userRow.querySelector('td:nth-child(2)').textContent;
                
                currentDeleteUserId = userId;
                document.getElementById('deleteUserName').textContent = userName;
                document.getElementById('deleteConfirmModal').classList.remove('modal-hidden');
            });
        });

        async function deleteUser() {
            if (!currentDeleteUserId) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value;
            
            try {
                const response = await fetch(`/admin/users/${currentDeleteUserId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    closeDeleteModal();
                    location.reload();
                } else {
                    alert('❌ ' + (data.message || 'Failed to delete user'));
                    closeDeleteModal();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error deleting user: ' + error.message);
                closeDeleteModal();
            }
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.add('modal-hidden');
            currentDeleteUserId = null;
        }

        // Close delete modal when clicking outside
        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>

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

    <script>
        // Attach button listeners AFTER buttons exist in DOM
        setTimeout(function() {
            const cancelBtn = document.getElementById('cancelDeleteBtn');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeDeleteModal();
                });
            }
            
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    deleteUser();
                });
            }
        }, 100);
    </script>

@endsection