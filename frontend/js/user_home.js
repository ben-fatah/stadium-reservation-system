// Logout
document.getElementById('logoutBtn').addEventListener('click', () => {
    fetch('../backend/Authentication/logout.php').then(()=> {
        window.location.href = 'index.html';
    });
});

// Search stadiums
document.getElementById('searchForm').addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);

    fetch(`../backend/Reservations/search.php?${params}`)
    .then(res => res.json())
    .then(data => {
        const tbody = document.querySelector('#stadiumTable tbody');
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
                    ${s.status === 'available' ? `<button class="btn btn-success btn-sm" onclick="reserve(${s.slot_id})">Reserve</button>` : ''}
                </td>
            </tr>`;
        });
    });
});

// Reserve slot
function reserve(slotId){
    const formData = new FormData();
    formData.append('slot_id', slotId);

    fetch('../backend/Reservations/reserve.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        document.getElementById('searchForm').dispatchEvent(new Event('submit')); // refresh table
    });
}
