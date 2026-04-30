<?php
$conn = new mysqli("localhost","root","","bus_ticket");
if ($conn->connect_error) {
    die("Connection failed");
}

/* Get bus id from URL */
$bus_id = isset($_GET['bus_id']) ? intval($_GET['bus_id']) : 0;

/* Fetch bus details */
$result = $conn->query("SELECT * FROM buses WHERE id = '$bus_id'");
$bus = $result->fetch_assoc();

/* Check if bus exists */
if (!$bus) {
    die("<h2 style='color:red;text-align:center;margin-top:50px;'>
         Invalid Bus ID. Please go back and select a bus.
         </h2>");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Bus</title>
<style>
body{font-family:Arial;background:#f2f2f2;}
.container{
    width:450px;
    margin:50px auto;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 10px 20px rgba(0,0,0,0.2);
}
h2{color:#2196f3;text-align:center;margin-bottom:20px;}
input,button{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
}
button{
    background:linear-gradient(135deg,#2196f3,#1976d2);
    color:white;
    border:none;
    cursor:pointer;
}
button:hover{transform:scale(1.05);}
.info{margin:5px 0;font-weight:600;}
</style>
</head>

<body>

<div class="container">
<h2>Book Bus: <?= $bus['bus_name'] ?></h2>

<div class="info">Route: <?= $bus['source'] ?> → <?= $bus['destination'] ?></div>
<div class="info">Available Seats: <?= $bus['available_seats'] ?></div>
<div class="info">Fare per Seat: ₹<?= $bus['fare'] ?></div>

<!-- Booking Form -->
<form method="post" action="bus_payment.php">
    <input type="hidden" name="bus_id" value="<?= $bus['id'] ?>">

    <input type="number"
           name="seats"
           min="1"
           max="<?= $bus['available_seats'] ?>"
           placeholder="Number of Seats"
           required>

    <input type="text"
           name="passenger_name"
           placeholder="Passenger Name"
           required>

    <input type="date"
           name="journey_date"
           required>

    <button>Proceed to Payment</button>
</form>
</div>

</body>
</html>
