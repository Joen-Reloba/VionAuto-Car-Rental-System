// User Management Modal Logic
const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ||
    document.querySelector('input[name="_token"]')?.value;
let currentDeleteId = null;
let currentDeleteType = null;

// ===== MODAL FUNCTIONS =====
function filterTable() {
    const searchTerm = document
        .getElementById("searchInput")
        .value.toLowerCase();
    const roleFilter = document.getElementById("roleFilter").value;
    const rows = document.querySelectorAll(
        "#userTable tbody tr:not(#emptyRow)",
    );
    let visibleCount = 0;

    rows.forEach((row) => {
        const searchData = row.getAttribute("data-search") || "";
        const role = row.getAttribute("data-role") || "";
        const matchesSearch = searchData.includes(searchTerm);
        const matchesRole = roleFilter === "all" || role === roleFilter;

        if (matchesSearch && matchesRole) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });

    document.getElementById("emptyRow").style.display =
        visibleCount === 0 ? "" : "none";
}

function closeAddModal() {
    document.getElementById("addUserModal").classList.remove("show");
}

function closeViewModal() {
    document.getElementById("viewUserModal").classList.remove("show");
}

function closeEditModal() {
    document.getElementById("editUserModal").classList.remove("show");
}

function closeDeleteModal() {
    document.getElementById("deleteConfirmModal").classList.remove("show");
    currentDeleteId = null;
    currentDeleteType = null;
}

async function confirmDelete() {
    if (!currentDeleteId) return;

    try {
        const response = await fetch(`/admin/users/${currentDeleteId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-Token": csrfToken,
            },
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert("Error: " + (data.message || "Failed to delete user"));
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Error deleting user");
    }

    closeDeleteModal();
}

// ===== INITIALIZE ON DOM READY =====
document.addEventListener("DOMContentLoaded", function () {
    // ===== SEARCH & FILTER =====
    document
        .getElementById("searchInput")
        .addEventListener("keyup", function () {
            filterTable();
        });

    document
        .getElementById("roleFilter")
        .addEventListener("change", function () {
            filterTable();
        });

    // ===== ADD USER =====
    document
        .getElementById("addUserBtn")
        .addEventListener("click", function () {
            document.getElementById("addUserForm").reset();
            document.getElementById("addUserModal").classList.add("show");
            document.getElementById("staffFields").style.display = "none"; // Hide staff fields on modal open
        });

    // Toggle staff-specific fields based on role selection
    document.getElementById("role").addEventListener("change", function () {
        const staffFields = document.getElementById("staffFields");
        const employeeNo = document.getElementById("employeeNo");

        if (this.value === "staff") {
            staffFields.style.display = "block";
            employeeNo.required = true;
        } else {
            staffFields.style.display = "none";
            employeeNo.required = false;
        }
    });

    document
        .getElementById("addUserForm")
        .addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Log CSRF token and form data for debugging
            console.log("CSRF Token:", csrfToken);
            console.log("Form Data:", data);

            try {
                const response = await fetch("/admin/users", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-Token": csrfToken,
                    },
                    body: JSON.stringify(data),
                });

                console.log("Response Status:", response.status);

                // Check if response is JSON
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const htmlText = await response.text();
                    console.error(
                        "Server returned non-JSON response:",
                        htmlText.substring(0, 1000),
                    );
                    alert(
                        "Server error: " +
                            response.status +
                            " " +
                            response.statusText,
                    );
                    return;
                }

                const responseData = await response.json();
                console.log("Response Data:", responseData);

                if (responseData.success) {
                    alert(responseData.message);
                    location.reload();
                } else {
                    const errorMsg =
                        responseData.message || "Failed to create user";
                    const errors = responseData.errors
                        ? "\n\nValidation errors:\n" +
                          JSON.stringify(responseData.errors, null, 2)
                        : "";
                    alert("Error: " + errorMsg + errors);
                }
            } catch (error) {
                console.error("Error creating user:", error);
                alert("Error creating user: " + error.message);
            }
        });

    // ===== VIEW USER =====
    document.querySelectorAll(".view-btn").forEach((btn) => {
        btn.addEventListener("click", async function () {
            const userId = this.getAttribute("data-id");

            try {
                const response = await fetch(`/admin/users/${userId}`);
                const data = await response.json();
                const user = data.user;
                const fullName = data.full_name;

                const content = `
                    <div class="field-group">
                        <span class="field-label">ID</span>
                        <div class="field-value">${user.user_ID}</div>
                    </div>
                    <div class="field-group">
                        <span class="field-label">Full Name</span>
                        <div class="field-value">${fullName}</div>
                    </div>
                    <div class="field-group">
                        <span class="field-label">Email</span>
                        <div class="field-value">${user.email}</div>
                    </div>
                    <div class="field-group">
                        <span class="field-label">Phone</span>
                        <div class="field-value">${user.phone_number}</div>
                    </div>
                    <div class="field-group">
                        <span class="field-label">Created At</span>
                        <div class="field-value">${new Date(user.created_at).toLocaleDateString()}</div>
                    </div>
                `;

                document.getElementById("viewUserContent").innerHTML = content;
                document.getElementById("viewUserModal").classList.add("show");
            } catch (error) {
                console.error("Error:", error);
                alert("Error loading user details");
            }
        });
    });

    // ===== EDIT USER =====
    document.querySelectorAll(".edit-btn").forEach((btn) => {
        btn.addEventListener("click", async function () {
            const userId = this.getAttribute("data-id");

            try {
                const userResponse = await fetch(`/admin/users/${userId}`);
                const userData = await userResponse.json();
                const user = userData.user;

                document.getElementById("editUserId").value = user.user_ID;
                document.getElementById("editFirstName").value =
                    user.first_name;
                document.getElementById("editMiddleName").value =
                    user.middle_name || "";
                document.getElementById("editLastName").value = user.last_name;
                document.getElementById("editUsername").value = user.username;
                document.getElementById("editEmail").value = user.email;
                document.getElementById("editRole").value = user.role || "";
                document.getElementById("editStatus").value = user.status || "";
                document.getElementById("editPhone").value = user.phone_number;
                document.getElementById("editPassword").value = "";

                // Show/hide staff fields based on role
                const editStaffFields =
                    document.getElementById("editStaffFields");
                if (user.role === "staff") {
                    editStaffFields.style.display = "block";
                    // Load staff data
                    const staff = userData.staff;
                    if (staff) {
                        document.getElementById("editEmployeeNo").value =
                            staff.employee_no || "";
                        document.getElementById("editPosition").value =
                            staff.position || "";
                        document.getElementById("editHiredAt").value =
                            staff.hired_at ? staff.hired_at.split(" ")[0] : "";
                    }
                } else {
                    editStaffFields.style.display = "none";
                }

                document.getElementById("editUserModal").classList.add("show");
            } catch (error) {
                console.error("Error:", error);
                alert("Error loading user");
            }
        });
    });

    // Toggle staff fields in edit modal based on role selection
    document.getElementById("editRole").addEventListener("change", function () {
        const editStaffFields = document.getElementById("editStaffFields");
        const editEmployeeNo = document.getElementById("editEmployeeNo");

        if (this.value === "staff") {
            editStaffFields.style.display = "block";
            editEmployeeNo.required = true;
        } else {
            editStaffFields.style.display = "none";
            editEmployeeNo.required = false;
        }
    });

    document
        .getElementById("editUserForm")
        .addEventListener("submit", async function (e) {
            e.preventDefault();

            const userId = document.getElementById("editUserId").value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-Token": csrfToken,
                        "X-HTTP-Method-Override": "PUT",
                    },
                    body: JSON.stringify(data),
                });

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const htmlText = await response.text();
                    console.error(
                        "Server returned non-JSON response:",
                        htmlText.substring(0, 1000),
                    );
                    alert("Server error: " + response.status);
                    return;
                }

                const responseData = await response.json();

                if (responseData.success) {
                    alert(responseData.message);
                    location.reload();
                } else {
                    const errorMsg =
                        responseData.message || "Failed to update user";
                    const errors = responseData.errors
                        ? "\n\nValidation errors:\n" +
                          JSON.stringify(responseData.errors, null, 2)
                        : "";
                    alert("Error: " + errorMsg + errors);
                }
            } catch (error) {
                console.error("Error updating user:", error);
                alert("Error updating user: " + error.message);
            }
        });

    // ===== DELETE CONFIRMATION =====
    document.querySelectorAll(".delete-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            currentDeleteId = this.getAttribute("data-id");
            currentDeleteType = this.getAttribute("data-type");
            document.getElementById("deleteConfirmModal").classList.add("show");
        });
    });

    // ===== CLOSE MODALS ON OUTSIDE CLICK =====
    window.addEventListener("click", function (event) {
        const addModal = document.getElementById("addUserModal");
        const viewModal = document.getElementById("viewUserModal");
        const editModal = document.getElementById("editUserModal");
        const deleteModal = document.getElementById("deleteConfirmModal");

        if (event.target === addModal) closeAddModal();
        if (event.target === viewModal) closeViewModal();
        if (event.target === editModal) closeEditModal();
        if (event.target === deleteModal) closeDeleteModal();
    });
});
