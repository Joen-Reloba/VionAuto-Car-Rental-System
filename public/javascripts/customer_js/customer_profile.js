function openPasswordModal() {
    document.getElementById("passwordModal").classList.add("open");
}
function closePasswordModal() {
    document.getElementById("passwordModal").classList.remove("open");
}
function openEditModal() {
    document.getElementById("editProfileModal").classList.add("open");
}
function closeEditModal() {
    document.getElementById("editProfileModal").classList.remove("open");
}

function openIdViewer(src) {
    document.getElementById("lightboxImg").src = src;
    document.getElementById("idLightbox").classList.add("open");
}
function closeIdViewer() {
    document.getElementById("idLightbox").classList.remove("open");
}

function togglePw(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === "password" ? "text" : "password";
    btn.classList.toggle("active");
}

function filterBookingTable(q) {
    document.querySelectorAll("#bookingTable tbody tr").forEach((r) => {
        r.style.display = r.textContent.toLowerCase().includes(q.toLowerCase())
            ? ""
            : "none";
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const appData = document.getElementById("appData");

    if (appData.dataset.openEdit === "true") openEditModal();
    if (appData.dataset.openPassword === "true") openPasswordModal();
});
