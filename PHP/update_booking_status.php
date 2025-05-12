<?php
require_once 'config.php'; // Include your database configuration

// Update bookings where end_date has passed and status isn't already "Completed"
$sql = "UPDATE bookings 
        SET booking_status = 'Completed' 
        WHERE end_date < CURDATE() 
        AND booking_status IN ('Paid', 'Pending')";

$result = $conn->query($sql);

if ($result) {
    $affected_rows = $conn->affected_rows;
    echo "Updated $affected_rows booking(s) to 'Completed' status";
} else {
    echo "Error updating bookings: " . $conn->error;
}

$conn->close();
?>