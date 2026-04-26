{{-- ── EDIT PROFILE MODAL ── --}}
<div class="modal-overlay" id="editProfileModal" onclick="closeEditModal()">
    <div class="modal-box modal-box--wide" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3>Edit Profile</h3>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                        style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Date of Birth</label>
                    <input type="date" name="birthday" value="{{ old('birthday', $customer->birthday) }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Address</label>
                <input type="text" name="address" value="{{ old('address', $customer->address) }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">License No.</label>
                    <input type="text" name="license_no" value="{{ old('license_no', $customer->license_no) }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">License Expiry</label>
                    <input type="date" name="license_expiry" value="{{ old('license_expiry', $customer->license_expiry) }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Valid Government ID</label>
                <input type="file" name="valid_ID" accept="image/*"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                @if($customer->valid_ID)
                    <small style="color: #999; margin-top: 6px; display: block;">Current: {{ $customer->valid_ID }} — upload a new one to replace it</small>
                @endif
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()"
                    style="padding: 10px 24px; background: #e5e5e5; color: #333; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit;">
                    Cancel
                </button>
                <button type="submit"
                    style="padding: 10px 24px; background: #8a2be2; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>