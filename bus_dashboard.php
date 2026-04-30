<?php
$conn = new mysqli("localhost","root","","bus_ticket");

if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

/* Handle Booking */
if(isset($_POST['pay'])){

    $bus_id = $_POST['bus_id'];
    $passengers = $_POST['passengers'];
    $aadhars = $_POST['aadhars'];
    $seats_to_book = count($passengers);

    $bus = $conn->query("SELECT available_seats,fare FROM buses WHERE id=$bus_id")->fetch_assoc();

    if($bus['available_seats'] < $seats_to_book){

        echo "<script>alert('Not enough seats available!');</script>";

    } else {

        $pnr = strtoupper(substr(md5(uniqid()),0,8));

        $stmt = $conn->prepare("INSERT INTO bus_bookings (bus_id, passenger_name, aadhar_number, pnr) VALUES (?,?,?,?)");

        for($i=0;$i<$seats_to_book;$i++){

            $stmt->bind_param("isss",$bus_id,$passengers[$i],$aadhars[$i],$pnr);
            $stmt->execute();

        }

        $new_seats = $bus['available_seats'] - $seats_to_book;

        $conn->query("UPDATE buses SET available_seats=$new_seats WHERE id=$bus_id");

        echo "<script>alert('Booking Successful! Your Bus PNR: $pnr');</script>";
        echo "<script>window.location.href=window.location.href;</script>";
    }
}

/* Fetch buses */
$buses = $conn->query("SELECT * FROM buses");
?>

<!DOCTYPE html>
<html>
<head>

<title>Bus Reservation Dashboard</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI;
}

body{
display:flex;
background:#f2f2f2;
}

/* Sidebar */

.sidebar{
width:240px;
height:100vh;
background:linear-gradient(180deg,#ff9800,#ff5722);
color:white;
padding-top:30px;
position:fixed;
}

.sidebar h2{
text-align:center;
margin-bottom:40px;
}

.sidebar a{
display:flex;
align-items:center;
padding:15px 25px;
color:white;
text-decoration:none;
transition:0.3s;
}

.sidebar a i{
margin-right:12px;
}

.sidebar a:hover{
background:rgba(255,255,255,0.2);
padding-left:35px;
}

/* Main */

.main{
margin-left:240px;
width:100%;
}

header{
background:white;
padding:15px 30px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.profile{
display:flex;
align-items:center;
}

.profile img{
width:38px;
height:38px;
border-radius:50%;
margin-left:10px;
}

/* Bus Cards */

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:30px;
padding:40px;
}

.bus-card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 8px 20px rgba(0,0,0,0.1);
}

.bus-card h3{
margin-bottom:10px;
}

.bus-card button{
padding:10px 20px;
margin-top:10px;
border:none;
background:linear-gradient(135deg,#ff9800,#ff5722);
color:white;
border-radius:8px;
cursor:pointer;
}

.passenger-form input{
padding:8px;
margin:5px 0;
width:100%;
border-radius:6px;
border:1px solid #ccc;
}

.payment-form{
display:none;
background:#fff;
padding:20px;
margin-top:15px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

.payment-form input{
padding:8px;
margin:5px 0;
width:100%;
border-radius:6px;
border:1px solid #ccc;
}

</style>
</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

<h2>🚌 Bus Reservation</h2>

<a href="dashboard.php">
<i class="fa fa-home"></i> Dashboard
</a>

<a href="bus_dashboard.php">
<i class="fa fa-bus"></i> Bus Reservation
</a>

<a href="my_bookings.php">
<i class="fa fa-list"></i> My Bookings
</a>

<!-- ❌ Cancel Ticket removed here -->

<a href="print_ticket.php">
<i class="fa fa-print"></i> Print Ticket
</a>

<a href="logout.php">
<i class="fa fa-sign-out"></i> Logout
</a>

</div>

<!-- Main Content -->

<div class="main">

<header>

<h3>Available Buses</h3>

<div class="profile">
<span>My Profile</span>
<img src="https://i.imgur.com/0y0y0y0.png">
</div>

</header>

<div class="cards">

<?php while($b = $buses->fetch_assoc()){ ?>

<div class="bus-card">

<h3><?= $b['bus_name']?> (<?= $b['bus_no']?>)</h3>

<p><?= $b['source']?> → <?= $b['destination']?></p>

<p>Seats Available: <?= $b['available_seats']?></p>

<p>Fare: ₹<?= $b['fare']?></p>

<?php if($b['available_seats']>0){ ?>

<button onclick="showBookingForm(<?= $b['id']?>)">Book Bus</button>

<?php } else { ?>

<b style="color:red;">Full</b>

<?php } ?>

<form class="passenger-form" id="form-<?= $b['id']?>" method="post" style="display:none;">

<input type="hidden" name="bus_id" value="<?= $b['id']?>">

<div id="passengers-<?= $b['id']?>"></div>

<label>Number of seats</label>

<input type="number"
min="1"
max="<?= $b['available_seats']?>"
id="seat-count-<?= $b['id']?>"
oninput="generatePassengerFields(<?= $b['id']?>)">

<button type="button"
onclick="showPaymentForm(<?= $b['id']?>,<?= $b['fare']?>)">
Proceed to Payment
</button>

</form>

<form class="payment-form" id="payment-<?= $b['id']?>" method="post">

<input type="hidden" name="bus_id" value="<?= $b['id']?>">

<input type="hidden"
name="total_fare"
id="total-fare-<?= $b['id']?>">

<div id="payment-passengers-<?= $b['id']?>"></div>

<label>Card Number</label>
<input type="text" required>

<label>Expiry</label>
<input type="text" required>

<label>CVV</label>
<input type="text" required>

<button type="submit" name="pay">Pay Now</button>

</form>

</div>

<?php } ?>

</div>

</div>

<script>

function showBookingForm(id){
document.getElementById("form-"+id).style.display="block";
}

function generatePassengerFields(id){

let count=document.getElementById("seat-count-"+id).value;

let container=document.getElementById("passengers-"+id);

container.innerHTML="";

for(let i=1;i<=count;i++){

container.innerHTML+=`
<h4>Passenger ${i}</h4>
<input type="text" name="passengers[]" placeholder="Passenger Name" required>
<input type="text" name="aadhars[]" placeholder="Aadhar Number" required>
`;

}

}

function showPaymentForm(id,fare){

let count=document.getElementById("seat-count-"+id).value;

document.getElementById("payment-passengers-"+id).innerHTML=
document.getElementById("passengers-"+id).innerHTML;

document.getElementById("total-fare-"+id).value=count*fare;

document.getElementById("payment-"+id).style.display="block";

}

</script>

</body>
</html>