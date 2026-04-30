<?php

$conn = new mysqli("localhost","root","","ticket_system");
if($conn->connect_error) die("Connection failed");

$id = $_GET['id'];

$booking = $conn->query("SELECT * FROM bookings WHERE id=$id")->fetch_assoc();

$train_id = $booking['train_id'];

$conn->query("DELETE FROM bookings WHERE id=$id");

$conn->query("UPDATE trains SET available_seats = available_seats + 1 WHERE id=$train_id");

echo "<script>alert('Ticket Cancelled Successfully');</script>";
echo "<script>window.location.href='my_bookings.php';</script>";

?>