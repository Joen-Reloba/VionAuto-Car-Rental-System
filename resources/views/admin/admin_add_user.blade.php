<div class="modal-header">
    <span>Add New User</span>
    <button class="modal-close" onclick="closeAddModal()">&times;</button>
</div>
<form id="addUserForm">
    @csrf
    <div class="form-group">
        <label for="firstName">First Name *</label>
        <input type="text" id="firstName" name="first_name" required>
    </div>
    <div class="form-group">
        <label for="middleName">Middle Name</label>
        <input type="text" id="middleName" name="middle_name">
    </div>
    <div class="form-group">
        <label for="lastName">Last Name *</label>
        <input type="text" id="lastName" name="last_name" required>
    </div>
    <div class="form-group">
        <label for="username">Username *</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone Number *</label>
        <input type="text" id="phone" name="phone_number" required>
    </div>
    <div class="form-group">
        <label for="password">Password *</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="btn-primary">Create User</button>
    </div>
</form>
