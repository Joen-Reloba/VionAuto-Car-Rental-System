const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
let currentDeleteUserId = null;

// ===== HELPERS =====
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove("modal-hidden");
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add("modal-hidden");
}

function closeAddModal() {
    closeModal("addUserModal");
    const form = document.getElementById("addUserForm");
    if (form) form.reset();
    const staffFields = document.getElementById("staffFields");
    if (staffFields) staffFields.style.display = "none";
}

function closeEditModal() {
    closeModal("editUserModal");
}

function closeUserModal() {
    closeModal("userModal");
}

function closeDeleteModal() {
    closeModal("deleteConfirmModal");
    currentDeleteUserId = null;
}

// ===== SEARCH & FILTER =====
function filterTable() {
    const searchTerm =
        document.getElementById("searchInput")?.value.toLowerCase() || "";
    const roleFilter = document.getElementById("roleFilter")?.value || "all";
    const rows = document.querySelectorAll(".user-table tbody tr");

    rows.forEach((row) => {
        const searchData = row.getAttribute("data-search") || "";
        const role = row.getAttribute("data-role") || "";
        const matchesSearch =
            searchData.includes(searchTerm) ||
            row.textContent.toLowerCase().includes(searchTerm);
        const matchesRole = roleFilter === "all" || role === roleFilter;
        row.style.display = matchesSearch && matchesRole ? "" : "none";
    });
}

// ===== ADD USER =====
function initAddUser() {
    const addBtn = document.getElementById("addUserBtn");
    if (addBtn) {
        addBtn.addEventListener("click", function () {
            const form = document.getElementById("addUserForm");
            if (form) form.reset();
            const staffFields = document.getElementById("staffFields");
            if (staffFields) staffFields.style.display = "none";
            openModal("addUserModal");
        });
    }

    const roleSelect = document.getElementById("role");
    if (roleSelect) {
        roleSelect.addEventListener("change", function () {
            const staffFields = document.getElementById("staffFields");
            const employeeNo = document.getElementById("employeeNo");
            if (staffFields)
                staffFields.style.display =
                    this.value === "staff" ? "block" : "none";
            if (employeeNo) employeeNo.required = this.value === "staff";
        });
    }

    const addForm = document.getElementById("addUserForm");
    if (addForm) {
        addForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            try {
                const response = await fetch("/admin/users", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    body: new FormData(this),
                });

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error(
                        "Non-JSON response:",
                        text.substring(0, 1000),
                    );
                    alert("Server error: " + response.status);
                    return;
                }

                const data = await response.json();
                console.log("Response:", data);

                if (data.success) {
                    alert("✅ User created successfully");
                    closeAddModal();
                    location.reload();
                } else {
                    const errors = data.errors
                        ? "\n\n" + Object.values(data.errors).flat().join("\n")
                        : "";
                    alert(
                        "❌ " +
                            (data.message || "Failed to create user") +
                            errors,
                    );
                }
            } catch (error) {
                console.error("Error:", error);
                alert("❌ Error: " + error.message);
            }
        });
    }

    const addModal = document.getElementById("addUserModal");
    if (addModal) {
        addModal.addEventListener("click", function (e) {
            if (e.target === this) closeAddModal();
        });
    }
}

// ===== VIEW USER =====
function initViewUser() {
    document.querySelectorAll(".view-btn").forEach((btn) => {
        btn.addEventListener("click", async function () {
            const userId =
                this.getAttribute("data-id") ||
                this.closest("tr")?.getAttribute("data-user-id");

            try {
                const response = await fetch(`/admin/users/${userId}`);
                const data = await response.json();
                const user = data.user;
                const staff = data.staff;
                const customer = data.customer;

                document.getElementById("modalUserId").textContent =
                    user.user_ID;
                document.getElementById("modalName").textContent =
                    data.full_name;
                document.getElementById("modalPhone").textContent =
                    user.phone_number || "-";
                document.getElementById("modalEmail").textContent =
                    user.email || "-";
                document.getElementById("modalUsername").textContent =
                    user.username || "-";
                document.getElementById("modalRole").textContent =
                    user.role.charAt(0).toUpperCase() + user.role.slice(1);
                document.getElementById("modalStatus").textContent =
                    user.status.charAt(0).toUpperCase() + user.status.slice(1);

                const staffSection = document.getElementById("staffSection");
                const customerSection =
                    document.getElementById("customerSection");

                if (user.role === "staff" && staff) {
                    staffSection.style.display = "block";
                    document.getElementById("modalEmployeeNo").textContent =
                        staff.employee_no || "-";
                    document.getElementById("modalPosition").textContent =
                        staff.position || "-";
                    document.getElementById("modalHiredAt").textContent =
                        staff.hired_at
                            ? new Date(staff.hired_at).toLocaleDateString()
                            : "-";
                    customerSection.style.display = "none";
                } else if (user.role === "customer" && customer) {
                    customerSection.style.display = "block";
                    document.getElementById("modalBirthday").textContent =
                        customer.birthday || "-";
                    document.getElementById("modalAddress").textContent =
                        customer.address || "-";
                    document.getElementById("modalLicenseNo").textContent =
                        customer.license_no || "-";
                    document.getElementById("modalLicenseExpiry").textContent =
                        customer.license_expiry || "-";
                    const validIdImg = document.getElementById("modalValidId");
                    if (customer.valid_ID) {
                        validIdImg.src =
                            "/assets/images/valid-ids/" + customer.valid_ID;
                        validIdImg.style.display = "block";
                    } else {
                        validIdImg.src = "";
                        validIdImg.style.display = "none";
                    }
                    staffSection.style.display = "none";
                } else {
                    staffSection.style.display = "none";
                    customerSection.style.display = "none";
                }

                openModal("userModal");
            } catch (error) {
                console.error("Error:", error);
                alert("Error loading user details");
            }
        });
    });

    const userModal = document.getElementById("userModal");
    if (userModal) {
        userModal.addEventListener("click", function (e) {
            if (e.target === this) closeUserModal();
        });
    }
}

// ===== EDIT USER =====
function initEditUser() {
    document.querySelectorAll(".edit-btn").forEach((btn) => {
        btn.addEventListener("click", async function () {
            const userId = this.getAttribute("data-id");

            try {
                const response = await fetch(`/admin/users/${userId}`);
                const data = await response.json();
                const user = data.user;

                document.getElementById("editUserId").value = user.user_ID;
                document.getElementById("editFirstName").value =
                    user.first_name || "";
                document.getElementById("editMiddleName").value =
                    user.middle_name || "";
                document.getElementById("editLastName").value =
                    user.last_name || "";
                document.getElementById("editUsername").value =
                    user.username || "";
                document.getElementById("editEmail").value = user.email || "";
                document.getElementById("editPhone").value =
                    user.phone_number || "";
                document.getElementById("editPassword").value = "";
                document.getElementById("editRole").value = user.role || "";
                document.getElementById("editStatus").value = user.status || "";

                const editStaffFields =
                    document.getElementById("editStaffFields");
                if (editStaffFields) {
                    if (user.role === "staff" && data.staff) {
                        editStaffFields.style.display = "block";
                        document.getElementById("editEmployeeNo").value =
                            data.staff.employee_no || "";
                        document.getElementById("editPosition").value =
                            data.staff.position || "";
                        document.getElementById("editHiredAt").value = data
                            .staff.hired_at
                            ? data.staff.hired_at.split(" ")[0]
                            : "";
                    } else {
                        editStaffFields.style.display = "none";
                    }
                }

                openModal("editUserModal");
            } catch (error) {
                console.error("Error:", error);
                alert("Error loading user");
            }
        });
    });

    const editRole = document.getElementById("editRole");
    if (editRole) {
        editRole.addEventListener("change", function () {
            const editStaffFields = document.getElementById("editStaffFields");
            const editEmployeeNo = document.getElementById("editEmployeeNo");
            if (editStaffFields)
                editStaffFields.style.display =
                    this.value === "staff" ? "block" : "none";
            if (editEmployeeNo)
                editEmployeeNo.required = this.value === "staff";
        });
    }

    const editForm = document.getElementById("editUserForm");
    if (editForm) {
        editForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const userId = document.getElementById("editUserId").value;

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    body: new FormData(this),
                });

                const data = await response.json();

                if (data.success) {
                    alert("✅ User updated successfully");
                    closeEditModal();
                    location.reload();
                } else {
                    const errors = data.errors
                        ? "\n\n" + Object.values(data.errors).flat().join("\n")
                        : "";
                    alert(
                        "❌ " +
                            (data.message || "Failed to update user") +
                            errors,
                    );
                }
            } catch (error) {
                console.error("Error:", error);
                alert("❌ Error: " + error.message);
            }
        });
    }

    const editModal = document.getElementById("editUserModal");
    if (editModal) {
        editModal.addEventListener("click", function (e) {
            if (e.target === this) closeEditModal();
        });
    }
}

// ===== DELETE USER =====
function initDeleteUser() {
    document.querySelectorAll(".delete-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const userRow = this.closest("tr");
            currentDeleteUserId = this.getAttribute("data-id");
            const nameEl = userRow?.querySelector("td:nth-child(2)");
            const deleteUserName = document.getElementById("deleteUserName");
            if (deleteUserName && nameEl)
                deleteUserName.textContent = nameEl.textContent;
            openModal("deleteConfirmModal");
        });
    });

    const cancelBtn = document.getElementById("cancelDeleteBtn");
    if (cancelBtn) cancelBtn.addEventListener("click", closeDeleteModal);

    const confirmBtn = document.getElementById("confirmDeleteBtn");
    if (confirmBtn) {
        confirmBtn.addEventListener("click", async function () {
            if (!currentDeleteUserId) return;

            try {
                const response = await fetch(
                    `/admin/users/${currentDeleteUserId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                    },
                );

                const data = await response.json();

                if (data.success) {
                    alert("✅ " + data.message);
                    closeDeleteModal();
                    location.reload();
                } else {
                    alert("❌ " + (data.message || "Failed to delete user"));
                    closeDeleteModal();
                }
            } catch (error) {
                console.error("Error:", error);
                alert("❌ Error: " + error.message);
                closeDeleteModal();
            }
        });
    }

    const deleteModal = document.getElementById("deleteConfirmModal");
    if (deleteModal) {
        deleteModal.addEventListener("click", function (e) {
            if (e.target === this) closeDeleteModal();
        });
    }
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    if (searchInput) searchInput.addEventListener("keyup", filterTable);

    const roleFilter = document.getElementById("roleFilter");
    if (roleFilter) roleFilter.addEventListener("change", filterTable);

    initAddUser();
    initViewUser();
    initEditUser();
    initDeleteUser();
});
