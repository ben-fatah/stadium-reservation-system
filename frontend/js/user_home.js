document.addEventListener('DOMContentLoaded', () => {
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      fetch('../backend/Authentication/logout.php')
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            window.location.href = 'index.html'; // ✅ redirection instead of reload
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

  loadAvailableSlots();        // load all slots by default
  loadUserReservations();       // load my reservations
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

      data.forEach(slot => {
        const isAvailable = slot.status === 'available';

        tbody.innerHTML += `
          <tr class="${!isAvailable ? 'table-danger' : ''}">
            <td>${slot.name}</td>
            <td>${slot.location}</td>
            <td>${slot.date}</td>
            <td>${slot.time_slot}</td>
            <td>${slot.status}</td>
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

      if (!Array.isArray(data.reservations)) {
        console.error("Expected array of reservations, got:", data);
        return;
      }

      data.reservations.forEach(r => {
        tbody.innerHTML += `
          <tr>
            <td>${r.stadium_name}</td>
            <td>${r.date}</td>
            <td>${r.time_slot}</td>
            <td>${r.status}</td>
          </tr>`;
      });
    })
    .catch(err => console.error("Failed to load reservations:", err));
}
