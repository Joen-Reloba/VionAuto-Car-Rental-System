/* ─────────────────────────────────────
   staff_vehicles.js
   Handles: card rendering, search,
   filter, and modal open/close/delete
───────────────────────────────────── */

const TYPE_LABELS = {
    sedan: "Sedan",
    suv: "SUV",
    van: "Van",
    pickup: "Pickup Truck",
};

const STATUS_LABELS = {
    available: "Available",
    rented: "Rented",
    maintenance: "Maintenance",
    unavailable: "Unavailable",
};

/* ── Helpers ── */
function formatPrice(amount) {
    return (
        "₱" +
        Number(amount).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
    );
}

/* ── Build a single card HTML string ── */
function buildCard(car) {
    const imgHtml = car.image
        ? `<img class="card-img" src="${car.image}" alt="${car.brand} ${car.model}">`
        : `<div class="card-no-img">
               <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <rect x="2" y="7" width="20" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/>
                   <path d="M5 7l2-3h10l2 3" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                   <circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="1.5"/>
                   <circle cx="17" cy="18" r="2" stroke="currentColor" stroke-width="1.5"/>
               </svg>
           </div>`;

    const typeLabel = TYPE_LABELS[car.type] || car.type;
    const statusLabel = STATUS_LABELS[car.status] || car.status;

    return `
        <div class="car-card" data-id="${car.id}" role="button" tabindex="0" aria-label="View details for ${car.brand} ${car.model}">
            ${imgHtml}
            <div class="card-body">
                <span class="card-type-badge">${typeLabel}</span>
                <div class="card-top">
                    <div>
                        <div class="card-car-name">${car.brand} ${car.model}</div>
                        <div class="card-car-year">${car.year}</div>
                    </div>
                    <span class="status-pill ${car.status}">${statusLabel}</span>
                </div>
                <div class="card-footer">
                    <span class="card-price">${formatPrice(car.daily_rate)}<span style="font-size:11px;font-weight:500;color:#aaa;">/day</span></span>
                    <span class="card-date">${car.created_at}</span>
                </div>
            </div>
        </div>`;
}

/* ── Render grid based on active filters ── */
function filterCards() {
    const q = document.getElementById("searchInput").value.trim().toLowerCase();
    const status = document.getElementById("statusFilter").value;
    const type = document.getElementById("typeFilter").value;

    const filtered = carsData.filter((car) => {
        const matchQ =
            !q ||
            car.brand.toLowerCase().includes(q) ||
            car.model.toLowerCase().includes(q);
        const matchS = !status || car.status === status;
        const matchT = !type || car.type === type;
        return matchQ && matchS && matchT;
    });

    const grid = document.getElementById("carGrid");
    const bar = document.getElementById("resultsBar");

    bar.textContent = `Showing ${filtered.length} vehicle${filtered.length !== 1 ? "s" : ""}`;

    if (filtered.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="7" width="20" height="11" rx="2" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M5 7l2-3h10l2 3" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>
                    <circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="1.2"/>
                    <circle cx="17" cy="18" r="2" stroke="currentColor" stroke-width="1.2"/>
                </svg>
                No vehicles match your filters.
            </div>`;
    } else {
        grid.innerHTML = filtered.map(buildCard).join("");
        attachCardListeners();
    }
}

/* ── Attach click listeners to every card ── */
function attachCardListeners() {
    document.querySelectorAll(".car-card").forEach((card) => {
        card.addEventListener("click", () => {
            const id = parseInt(card.dataset.id);
            const car = carsData.find((c) => c.id === id);
            if (car) openModal(car);
        });
        card.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") card.click();
        });
    });
}

/* ════════════════════════════════
   ── Modal Logic ──
════════════════════════════════ */
let activeCar = null;

function openModal(car) {
    activeCar = car;

    /* Image */
    const img = document.getElementById("modalImage");
    const noImg = document.getElementById("modalNoImg");
    if (car.image) {
        img.src = car.image;
        img.alt = `${car.brand} ${car.model}`;
        img.classList.remove("hidden");
        noImg.style.display = "none";
    } else {
        img.classList.add("hidden");
        noImg.style.display = "flex";
    }

    /* Text fields */
    document.getElementById("modalCarName").textContent =
        `${car.brand} ${car.model}`;
    document.getElementById("modalTypeBadge").textContent =
        TYPE_LABELS[car.type] || car.type;
    document.getElementById("modalBrand").textContent = car.brand;
    document.getElementById("modalModel").textContent = car.model;
    document.getElementById("modalYear").textContent = car.year;
    document.getElementById("modalPrice").textContent =
        formatPrice(car.daily_rate) + "/day";
    document.getElementById("modalType").textContent =
        TYPE_LABELS[car.type] || car.type;
    document.getElementById("modalDate").textContent = car.created_at;
    document.getElementById("modalDescription").textContent =
        car.description || "—";

    /* Status pill */
    const pill = document.getElementById("modalStatusPill");
    pill.textContent = STATUS_LABELS[car.status] || car.status;
    pill.className = `status-pill ${car.status}`;

    /* Show overlay */
    document.getElementById("modalOverlay").classList.add("active");
    document.body.style.overflow = "hidden";
}

function closeModal() {
    document.getElementById("modalOverlay").classList.remove("active");
    document.body.style.overflow = "";
    activeCar = null;
}

/* ── Edit/Update button → redirect to update page ── */
document.getElementById("modalEditBtn").addEventListener("click", () => {
    if (!activeCar) return;
    window.location.href = routes.edit.replace(":id", activeCar.id);
});

function openEditModal(car) {
    // No longer used - keeping for reference
}

/* ── Delete button → confirm then POST ── */
document.getElementById("modalDeleteBtn").addEventListener("click", () => {
    if (!activeCar) return;
    if (
        !confirm(
            `Delete ${activeCar.brand} ${activeCar.model}? This cannot be undone.`,
        )
    )
        return;

    const url = routes.delete.replace(":id", activeCar.id);
    const form = document.createElement("form");
    form.method = "POST";
    form.action = url;

    const csrf = document.createElement("input");
    csrf.type = "hidden";
    csrf.name = "_token";
    csrf.value = csrfToken;

    const method = document.createElement("input");
    method.type = "hidden";
    method.name = "_method";
    method.value = "DELETE";

    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
});

/* ── Close on overlay backdrop click ── */
document.getElementById("modalOverlay").addEventListener("click", (e) => {
    if (e.target === e.currentTarget) closeModal();
});

/* ── Close button ── */
document.getElementById("modalClose").addEventListener("click", closeModal);

/* ── Close on Escape ── */
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
});

/* ── Search input: also trigger on Enter ── */
document.getElementById("searchInput").addEventListener("keydown", (e) => {
    if (e.key === "Enter") filterCards();
});

/* ── Add vehicle button ── */
document.getElementById("addVehicleBtn").addEventListener("click", () => {
    window.location.href = "/staff/vehicles/create";
});

/* ── Initial render ── */
filterCards();
