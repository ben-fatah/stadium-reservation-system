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
      loadAvailableSlots(location, date);
    });
  }

  loadAvailableSlots();
  loadUserReservations();
});

function loadAvailableSlots(location = '', date = '') {
  fetch('../backend/Stadiums_Slots/get_slots.php' + `?location=${encodeURIComponent(location)}&date=${encodeURIComponent(date)}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#stadiumTable tbody');
      tbody.innerHTML = '';

      if (!Array.isArray(data)) {
        console.error("Expected array of slots, got:", data);
        return;
      }

      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No slots found</td></tr>';
        return;
      }

      data.forEach(slot => {
        const isAvailable = slot.status === 'available';

        tbody.innerHTML += `
          <tr class="${!isAvailable ? 'table-secondary' : ''}">
            <td>${slot.name}</td>
            <td>${slot.location}</td>
            <td>${slot.date}</td>
            <td>${slot.time_slot}</td>
            <td><span class="badge ${isAvailable ? 'bg-success' : 'bg-danger'}">${slot.status}</span></td>
            <td>
              ${isAvailable ? `<button class="btn btn-sm btn-primary" onclick="bookSlot(${slot.slot_id})">Reserve</button>` : ''}
            </td>
          </tr>`;
      });
    })
    .catch(err => {
      console.error("Failed to load slots:", err);
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
  .catch(err => console.error("Reservation error:", err));
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
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">You have no reservations yet</td></tr>';
        return;
      }

      data.reservations.forEach(r => {
        tbody.innerHTML += `
          <tr>
            <td>${r.stadium_name}</td>
            <td>${r.date}</td>
            <td>${r.time_slot}</td>
            <td><span class="badge bg-success">${r.status}</span></td>
            <td>
              <button class="btn btn-sm btn-danger" onclick="cancelReservation(${r.slot_id})">
                Cancel
              </button>
            </td>
          </tr>`;
      });
    })
    .catch(err => console.error("Failed to load reservations:", err));
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
  .catch(err => console.error("Cancel error:", err));
}