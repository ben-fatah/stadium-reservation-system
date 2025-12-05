// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
    // Logout
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
                    alert("An error occurred during logout.");
                });
        });
    }

    // Load stadiums initially
    loadStadiums();

    // Add stadium
    const addStadiumForm = document.getElementById('addStadiumForm');
    if (addStadiumForm) {
        addStadiumForm.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch('../backend/Stadiums_Slots/add_stadium.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        e.target.reset();
                        loadStadiums();
                    }
                })
                .catch(err => {
                    console.error("Add stadium error:", err);
                    alert("Failed to add stadium.");
                });
        });
    }
});

// Load stadiums function
function loadStadiums() { 
    fetch('../backend/Stadiums_Slots/dashboard.php') 
        .then(res => res.json()) 
        .then(data => { 
            const tbody = document.querySelector('#stadiumTable tbody'); 
            if (tbody) { 
                tbody.innerHTML = ''; 
                data.forEach(stadium => { 
                    tbody.innerHTML += ` 
                        <tr> 
                            <td>${stadium.name}</td> 
                            <td>${stadium.location}</td> 
                            <td>${stadium.description || ''}</td> 
                            <td><img src="${stadium.photo}" alt="Stadium Photo" width="100" height="60" style="object-fit: cover;"></td> 
                            <td>${stadium.total_slots || 0}</td> 
                            <td>${stadium.reserved_slots || 0}</td> 
                        </tr>`; 
                }); 
            } 
        }) 
        .catch(err => { 
            console.error("Failed to load stadiums:", err); 
        }); 
}

