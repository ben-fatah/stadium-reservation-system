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
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No stadiums added yet</td></tr>';
                    return;
                }
                
                data.forEach(stadium => {  
                    tbody.innerHTML += `  
                        <tr>  
                            <td>${stadium.name}</td>  
                            <td>${stadium.location}</td>  
                            <td>${stadium.description || ''}</td>  
                            <td><img src="${stadium.photo}" alt="Stadium Photo" width="100" height="60" style="object-fit: cover; border-radius: 8px;"></td>  
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
 
function loadStadiumSelect() { 
    fetch('../backend/Stadiums_Slots/get_stadiums.php') 
        .then(res => res.json()) 
        .then(data => { 
            const select = document.getElementById('stadiumSelect'); 
            if (select) { 
                select.innerHTML = '<option value="">Select Stadium</option>'; 
                if (data.stadiums && data.stadiums.length > 0) {
                    data.stadiums.forEach(stadium => { 
                        const option = document.createElement('option'); 
                        option.value = stadium.id; 
                        option.textContent = stadium.name; 
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
            
            data.forEach(stadium => {
                totalReservations += parseInt(stadium.reserved_slots || 0);
            });
            
            document.getElementById('totalStadiums').textContent = totalStadiums;
            document.getElementById('totalReservations').textContent = totalReservations;
        })
        .catch(err => {
            console.error("Failed to load statistics:", err);
        });
}