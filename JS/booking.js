document.addEventListener('DOMContentLoaded', () => {
    const roomTypeSelect = document.getElementById('room-type');
    const roomQuantityContainer = document.getElementById('room-quantity-container');
    const roomQuantitySelect = document.getElementById('room-quantity');
    const roomIdContainer = document.getElementById('room-id-container');
    const roomIdSelect = document.getElementById('room-id');

    const roomData = {
        'Standard': 10,
        'Deluxe': 6,
        'Executive': 4,
        'Penthouse': 2
    };

    roomTypeSelect.addEventListener('change', () => {
        const selectedRoomType = roomTypeSelect.value;
        
        if (selectedRoomType) {
            const maxRooms = roomData[selectedRoomType];
            populateRoomQuantity(maxRooms);
            roomQuantityContainer.style.display = 'block';
            roomIdContainer.style.display = 'none';
        } else {
            roomQuantityContainer.style.display = 'none';
            roomIdContainer.style.display = 'none';
        }
    });

    roomQuantitySelect.addEventListener('change', () => {
        const selectedRoomType = roomTypeSelect.value;
        const selectedQuantity = roomQuantitySelect.value;
        
        if (selectedQuantity) {
            fetchRoomIds(selectedRoomType, selectedQuantity);
        } else {
            roomIdContainer.style.display = 'none';
        }
    });

    function populateRoomQuantity(maxRooms) {
        roomQuantitySelect.innerHTML = '';
        for (let i = 1; i <= maxRooms; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            roomQuantitySelect.appendChild(option);
        }
    }

    function fetchRoomIds(roomType, quantity) {
        const formData = new FormData();
        formData.append('roomType', roomType);
        formData.append('quantity', quantity);

        fetch('get_room_ids.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            populateRoomIds(data);
            roomIdContainer.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
    }

    function populateRoomIds(roomIds) {
        roomIdSelect.innerHTML = '';
        for (let i = 1; i <= roomIds.length; i++) {
            const select = document.createElement('select');
            select.name = `room-id-${i}`;
            select.required = true;
            roomIds.forEach(id => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = `Room ${id}`;
                select.appendChild(option);
            });
            roomIdSelect.appendChild(select);
        }
    }
});
