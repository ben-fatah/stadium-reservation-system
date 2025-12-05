// Logout
document.getElementById('logoutBtn').addEventListener('click', () => {
    fetch('../backend/Authentication/logout.php').then(()=> {
        window.location.href = 'index.html';
    });
});

// Fetch stadiums and stats
function loadStadiums() {
    fetch('../backend/Stadiums/dashboard.php')
    .then(res => res.json())
    .then(data => {
        const tbody = document.querySelector('#stadiumTable tbody');
        tbody.innerHTML = '';
        data.forEach(stadium => {
            tbody.innerHTML += `
            <tr>
                <td>${stadium.name}</td>
                <td>${stadium.location}</td>
                <td>${stadium.total_slots || 0}</td>
                <td>${stadium.reserved_slots || 0}</td>
            </tr>`;
        });
    });
}
loadStadiums();

// Add new stadium
document.getElementById('addStadiumForm').addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('../backend/Stadiums/add_stadium.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.status === 'success') e.target.reset();
        loadStadiums();
    });
});
