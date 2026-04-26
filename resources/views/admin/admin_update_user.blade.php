{{-- Edit User Modal --}}
<div id="editUserModal" class="modal modal-hidden">
    <div class="modal-content">
        <button class="modal-close" onclick="closeEditModal()">&times;</button>
        
        <h2 class="modal-title">Edit User</h2>
        
        <div class="modal-body">
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Role *</label>
                        <select id="editRole" name="role" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Status *</label>
                        <select id="editStatus" name="status" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Last Name *</label>
                    <input type="text" id="editLastName" name="last_name" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">First Name *</label>
                        <input type="text" id="editFirstName" name="first_name" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Middle Name</label>
                        <input type="text" id="editMiddleName" name="middle_name" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Username *</label>
                    <input type="text" id="editUsername" name="username" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Email *</label>
                    <input type="email" id="editEmail" name="email" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Phone Number *</label>
                    <input type="text" id="editPhone" name="phone_number" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #666; margin-bottom: 8px;">Password (leave blank to keep current)</label>
                    <input type="password" id="editPassword" name="password" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;">
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" style="padding: 10px 24px; background: #e5e5e5; color: #333; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit;" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" style="padding: 10px 24px; background: #8a2be2; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit;">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

