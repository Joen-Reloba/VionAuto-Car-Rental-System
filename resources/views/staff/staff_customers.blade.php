@extends('layouts.staff_layout')

@section('styles')
    @vite('resources/css/staff_css/staff_customers.css')
@endsection

@section('content')
<div class="customers-page">
     <div class="page-header">
                <h1 class="page-title">Customers</h1>
            </div>

            <div class="table-card">

                <div class="table-topbar">
                    <div class="search-wrap">
                        <input type="text" class="search-input" placeholder="Search by name or ID..." id="searchInput">
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                       <tbody>
                           @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer['user_ID'] }}</td>
                                    <td>{{ $customer['name'] }}</td>
                                    <td>{{ $customer['phone_number'] }}</td>
                                    <td>{{ $customer['email'] }}</td>
                                    <td><span class="status-pill active">✔ Active</span></td>
                                    <td class="action-cell">
                                        <button class="action-btn view-btn" onclick="viewCustomer('{{ $customer['user_ID'] }}')">
                                            <img src="{{asset('assets/icons/view.png')}}" alt="View">
                                        </button>
                                    </td>
                               </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">No customers found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($pagination->hasPages())
                    <div class="pagination-wrapper">
                        {{ $pagination->links('vendor.pagination.custom') }}
                    </div>
                @endif

            </div>
</div>

{{-- Customer Detail Modal --}}
<div id="customerModal" class="modal modal-hidden">
    <div class="modal-content">
        <button class="modal-close" onclick="closeCustomerModal()">&times;</button>
        
        <h2 class="modal-title">Customer Details</h2>
        
        <div class="modal-body">
            <div class="detail-section">
                <h3>Personal Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Customer ID</label>
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
                        <label>Birthday</label>
                        <p id="modalBirthday">-</p>
                    </div>
                    <div class="detail-item">
                        <label>Address</label>
                        <p id="modalAddress">-</p>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3>License Information</h3>
                <div class="detail-grid">
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
        </div>
    </div>
</div>

<script>
    const customersData = @json($customers);

    function viewCustomer(userId) {
        const customer = customersData.find(c => c.user_ID == userId);
        
        if (customer) {
            document.getElementById('modalUserId').textContent = customer.user_ID;
            document.getElementById('modalName').textContent = customer.name || '-';
            document.getElementById('modalPhone').textContent = customer.phone_number || '-';
            document.getElementById('modalEmail').textContent = customer.email || '-';
            document.getElementById('modalBirthday').textContent = customer.birthday || '-';
            document.getElementById('modalAddress').textContent = customer.address || '-';
            document.getElementById('modalLicenseNo').textContent = customer.license_no || '-';
            document.getElementById('modalLicenseExpiry').textContent = customer.license_expiry || '-';
            const validIdImg = document.getElementById('modalValidId');
            if (customer.valid_ID) {
                validIdImg.src = '/storage/images-valid_id/' + customer.valid_ID;
                validIdImg.style.display = 'block';
            } else {
                validIdImg.src = '';
                validIdImg.style.display = 'none';
            }
            
            const modal = document.getElementById('customerModal');
            modal.classList.remove('modal-hidden');
        }
    }

    function closeCustomerModal() {
        const modal = document.getElementById('customerModal');
        modal.classList.add('modal-hidden');
    }

    // Close modal when clicking outside
    document.getElementById('customerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCustomerModal();
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
</script>

@endsection