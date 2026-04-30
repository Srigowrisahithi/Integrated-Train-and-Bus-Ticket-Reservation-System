<?php
session_start();
$conn = new mysqli("localhost","root","","ticket_system");

$user_id = 1; // TEMP (replace with $_SESSION['user_id'])
$train_id = $_POST['train_id'];
$seats = $_POST['seats'];
$date = $_POST['date'];

$pnr = rand(1000000000,9999999999);

// Reduce seats
$conn->query("UPDATE trains 
SET available_seats = available_seats - $seats 
WHERE id = $train_id");

$stmt = $conn->prepare(
"INSERT INTO railway_bookings 
(user_id, train_id, journey_date, seats, pnr, status)
VALUES (?,?,?,?,?,?)");

$status="CONFIRMED";
$stmt->bind_param("iiisss",$user_id,$train_id,$date,$seats,$pnr,$status);
$stmt->execute();

echo "<h2>Booking Confirmed</h2>";
echo "PNR: <b>$pnr</b><br>";
echo "<a href='railway_dashboard.php'>Back</a>";
