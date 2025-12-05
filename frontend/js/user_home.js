// Logout
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            fetch('../backend/Authentication/logout.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'index.html';
                    } else {
                        alert("Logout failed: " + data.message);
                    }
                })
                .catch(err => {
                    console.error("Logout error:", err);
                    alert("An error occurred while logging out.");
                });
        });
    }
});

// Search stadiums
document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', e => {
            e.preventDefault();

            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData);

            fetch(`../backend/Reservations/search.php?${params}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector('#stadiumTable tbody');
                    if (tbody) {
                        tbody.innerHTML = '';
                        data.forEach(s => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${s.name}</td>
                                    <td>${s.location}</td>
                                    <td>${s.date || '-'}</td>
                                    <td>${s.time_slot || '-'}</td>
                                    <td>${s.status || 'available'}</td>
                                    <td>
                                        ${s.status === 'available'
                                            ? `<button class="btn btn-success btn-sm" onclick="reserve(${s.slot_id})">Reserve</button>`
                                            : ''}
                                    </td>
                                </tr>`;
                        });
                    }
                })
                .catch(err => {
                    console.error("Error fetching stadiums:", err);
                });
        });
    }
});

// Reserve slot
function reserve(slotId) {
    const formData = new FormData();
    formData.append('slot_id', slotId);

    fetch('../backend/Reservations/reserve.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            const searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.dispatchEvent(new Event('submit')); // Refresh table
            }
        })
        .catch(err => {
            console.error("Reservation error:", err);
            alert("Could not complete reservation.");
        });
}
