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

  const searchForm = document.getElementById('searchForm');
  if (searchForm) {
    searchForm.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(searchForm);
      const location = formData.get('location') || '';
      const date = formData.get('date') || '';
      const time_slot = formData.get('time_slot') || '';
      loadAvailableSlots(location, date, time_slot);
    });
  }

  loadAvailableSlots();
  loadUserReservations();
});

function loadAvailableSlots(location = '', date = '', time_slot = '') {
  const params = new URLSearchParams();
  if (location) params.append('location', location);
  if (date) params.append('date', date);
  if (time_slot) params.append('time_slot', time_slot);

  fetch('../backend/Stadiums_Slots/get_slots.php?' + params.toString())
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#stadiumTable tbody');
      tbody.innerHTML = '';

      if (!Array.isArray(data)) {
        console.error("Expected array of slots, got:", data);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading stadiums</td></tr>';
        return;
      }

      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No available slots found. Try different search criteria.</td></tr>';
        return;
      }

      data.forEach(slot => {
        const isAvailable = slot.status === 'available';
        const statusClass = isAvailable ? 'status-available' : 'status-reserved';
        const statusText = isAvailable ? '🟢 AVAILABLE' : '🔴 RESERVED';
        const photoUrl = slot.photo || 'https://via.placeholder.com/80x60?text=No+Image';

        tbody.innerHTML += `
          <tr class="${!isAvailable ? 'table-secondary' : ''}">
            <td><img src="${photoUrl}" alt="Stadium" class="stadium-img" onerror="this.src='https://via.placeholder.com/80x60?text=No+Image'"></td>
            <td><strong>${slot.name}</strong></td>
            <td>${slot.location}</td>
            <td>${slot.date}</td>
            <td>${slot.time_slot}</td>
            <td><span class="${statusClass}">${statusText}</span></td>
            <td>
              ${isAvailable ? 
                `<button class="btn btn-sm btn-reserve" onclick="bookSlot(${slot.slot_id})">
                  🎯 Reserve
                </button>` : 
                '<span class="text-muted">Not Available</span>'
              }
            </td>
          </tr>`;
      });
    })
    .catch(err => {
      console.error("Failed to load slots:", err);
      const tbody = document.querySelector('#stadiumTable tbody');
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Failed to load stadiums</td></tr>';
    });
}

function bookSlot(slotId) {
  if (!confirm('Do you want to reserve this slot?')) return;

  const formData = new FormData();
  formData.append('slot_id', slotId);

  fetch('../backend/Reservations/reserve.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.status === 'success') {
      loadAvailableSlots();
      loadUserReservations();
    }
  })
  .catch(err => {
    console.error("Reservation error:", err);
    alert("Failed to make reservation. Please try again.");
  });
}

function loadUserReservations() {
  fetch('../backend/Reservations/reservation.php')
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#reservationTable tbody');
      tbody.innerHTML = '';

      if (!data.reservations || !Array.isArray(data.reservations)) {
        console.error("Expected array of reservations, got:", data);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No reservations found</td></tr>';
        return;
      }

      if (data.reservations.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">You have no reservations yet. Search and book a stadium above! ⚽</td></tr>';
        return;
      }

      data.reservations.forEach(r => {
        const statusClass = r.status === 'reserved' ? 'status-reserved' : 'status-available';
        const statusText = r.status === 'reserved' ? '🔴 RESERVED' : '🟢 AVAILABLE';

        tbody.innerHTML += `
          <tr>
            <td><strong>${r.stadium_name}</strong></td>
            <td>${r.date}</td>
            <td>${r.time_slot}</td>
            <td><span class="${statusClass}">${statusText}</span></td>
            <td>
              ${r.status === 'reserved' ? 
                `<button class="btn btn-sm btn-danger" onclick="cancelReservation(${r.slot_id})">
                  ❌ Cancel
                </button>` : 
                '<span class="text-muted">-</span>'
              }
            </td>
          </tr>`;
      });
    })
    .catch(err => {
      console.error("Failed to load reservations:", err);
      const tbody = document.querySelector('#reservationTable tbody');
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load reservations</td></tr>';
    });
}

function cancelReservation(slotId) {
  if (!confirm('Are you sure you want to cancel this reservation?')) return;

  const formData = new FormData();
  formData.append('slot_id', slotId);

  fetch('../backend/Reservations/cancel.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.status === 'success') {
      loadAvailableSlots();
      loadUserReservations();
    }
  })
  .catch(err => {
    console.error("Cancel error:", err);
    alert("Failed to cancel reservation. Please try again.");
  });
}