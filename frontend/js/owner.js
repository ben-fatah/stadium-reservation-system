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
                    alert("An error occurred during logout."); 
                }); 
        }); 
    } 
 
    loadStadiums(); 
    loadStadiumSelect();
    loadStatistics();
    loadAllSlots();

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
                        loadStadiumSelect();
                        loadStatistics();
                    } 
                }) 
                .catch(err => { 
                    console.error("Add stadium error:", err); 
                    alert("Failed to add stadium."); 
                }); 
        }); 
    } 

    const addSlotForm = document.getElementById('addSlotForm'); 
    if (addSlotForm) { 
        addSlotForm.addEventListener('submit', e => { 
            e.preventDefault(); 
            const formData = new FormData(addSlotForm); 
 
            fetch('../backend/Stadiums_Slots/add_slots.php', { 
                method: 'POST', 
                body: formData 
            }) 
                .then(res => res.json()) 
                .then(data => { 
                    alert(data.message); 
                    if (data.status === 'success') { 
                        addSlotForm.reset();
                        loadStadiums();
                        loadStatistics();
                        loadAllSlots();
                    } 
                }) 
                .catch(err => { 
                    console.error("Add slot error:", err); 
                    alert("Failed to add slot."); 
                }); 
        }); 
    } 
}); 
 
function loadStadiums() {  
    fetch('../backend/Stadiums_Slots/dashboard.php')  
        .then(res => res.json())  
        .then(data => {  
            const tbody = document.querySelector('#stadiumTable tbody');  
            if (tbody) {  
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No stadiums added yet</td></tr>';
                    return;
                }
                
                data.forEach(stadium => {  
                    const totalSlots = parseInt(stadium.total_slots) || 0;
                    const reservedSlots = parseInt(stadium.reserved_slots) || 0;
                    const availableSlots = totalSlots - reservedSlots;

                    tbody.innerHTML += `  
                        <tr>  
                            <td><strong>${stadium.name}</strong></td>  
                            <td>${stadium.location}</td>  
                            <td>${stadium.description || 'N/A'}</td>  
                            <td><img src="${stadium.photo}" alt="Stadium" class="stadium-img"></td>  
                            <td><span class="badge bg-info">${totalSlots}</span></td>  
                            <td><span class="badge bg-danger">${reservedSlots}</span></td>  
                            <td><span class="badge bg-success">${availableSlots}</span></td>  
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="viewStadiumDetails(${stadium.id})">
                                    👁️ View
                                </button>
                            </td>
                        </tr>`;  
                });  
            }  
        })  
        .catch(err => {  
            console.error("Failed to load stadiums:", err);  
        });  
} 
 
function loadStadiumSelect() { 
    fetch('../backend/Stadiums_Slots/get_stadiums.php') 
        .then(res => res.json()) 
        .then(data => { 
            const select = document.getElementById('stadiumSelect'); 
            if (select) { 
                select.innerHTML = '<option value="">Choose Stadium...</option>'; 
                if (data.stadiums && data.stadiums.length > 0) {
                    data.stadiums.forEach(stadium => { 
                        const option = document.createElement('option'); 
                        option.value = stadium.id; 
                        option.textContent = stadium.name + ' - ' + stadium.location; 
                        select.appendChild(option); 
                    });
                }
            } 
        }) 
        .catch(err => { 
            console.error("Failed to load stadiums into select:", err); 
        }); 
}

function loadStatistics() {
    fetch('../backend/Stadiums_Slots/dashboard.php')
        .then(res => res.json())
        .then(data => {
            const totalStadiums = data.length;
            let totalReservations = 0;
            let totalSlots = 0;
            
            data.forEach(stadium => {
                totalReservations += parseInt(stadium.reserved_slots || 0);
                totalSlots += parseInt(stadium.total_slots || 0);
            });
            
            document.getElementById('totalStadiums').textContent = totalStadiums;
            document.getElementById('totalReservations').textContent = totalReservations;
            document.getElementById('totalSlots').textContent = totalSlots;
        })
        .catch(err => {
            console.error("Failed to load statistics:", err);
        });
}

function loadAllSlots() {
    fetch('../backend/Stadiums_Slots/get_owner_slots.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#slotsTable tbody');
            if (!tbody) return;

            tbody.innerHTML = '';

            if (!data.slots || data.slots.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No slots created yet</td></tr>';
                return;
            }

            data.slots.forEach(slot => {
                const statusClass = slot.status === 'available' ? 'status-available' : 'status-reserved';
                const statusText = slot.status === 'available' ? '🟢 AVAILABLE' : '🔴 RESERVED';
                const reservedBy = slot.reserved_by || '-';

                tbody.innerHTML += `
                    <tr>
                        <td><strong>${slot.stadium_name}</strong></td>
                        <td>${slot.date}</td>
                        <td>${slot.time_slot}</td>
                        <td><span class="${statusClass}">${statusText}</span></td>
                        <td>${reservedBy}</td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error("Failed to load slots:", err);
        });
}

function viewStadiumDetails(stadiumId) {
    alert('Stadium details view - Feature coming soon!');
    // You can implement a modal or new page to show detailed stats
}