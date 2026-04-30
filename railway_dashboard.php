<?php
$conn = new mysqli("localhost","root","","ticket_system");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Handle booking submission after payment
if(isset($_POST['pay'])) {
    $train_id = $_POST['train_id'];
    $passengers = $_POST['passengers']; // array of names
    $aadhars = $_POST['aadhars'];       // array of aadhar numbers
    $seats_to_book = count($passengers);
    $total_fare = $_POST['total_fare'];

    // Check seats again
    $train = $conn->query("SELECT available_seats, fare FROM trains WHERE id=$train_id")->fetch_assoc();
    if($train['available_seats'] < $seats_to_book){
        echo "<script>alert('Not enough seats available!');</script>";
    } else {
        // Generate a unique PNR
        $pnr = strtoupper(substr(md5(uniqid()),0,8));

        $stmt = $conn->prepare("INSERT INTO bookings (train_id, passenger_name, aadhar_number, pnr) VALUES (?,?,?,?)");
        for($i=0;$i<$seats_to_book;$i++){
            $stmt->bind_param("isss",$train_id,$passengers[$i],$aadhars[$i],$pnr);
            $stmt->execute();
        }

        // Update available seats
        $new_seats = $train['available_seats'] - $seats_to_book;
        $conn->query("UPDATE trains SET available_seats=$new_seats WHERE id=$train_id");

        // Show confirmation with PNR
        echo "<script>alert('Payment Successful! Your PNR: $pnr');</script>";
        echo "<script>window.location.href=window.location.href;</script>";
    }
}

// Fetch trains
$trains = $conn->query("SELECT * FROM trains");
?>

<!DOCTYPE html>
<html>
<head>
<title>Railway Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f2f2f2;display:flex;}
.sidebar{width:240px;height:100vh;background:linear-gradient(180deg,#ff9800,#ff5722);color:white;padding-top:30px;position:fixed;}
.sidebar h2{text-align:center;margin-bottom:40px;}
.sidebar a{display:flex;align-items:center;padding:15px 25px;color:white;text-decoration:none;transition:0.3s;}
.sidebar a i{margin-right:12px;}
.sidebar a:hover{background:rgba(255,255,255,0.2);padding-left:35px;}
.main{margin-left:240px;width:100%;}
header{background:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.search-container{position:relative;width:420px;}
.search-box{display:flex;align-items:center;background:#f1f1f1;border-radius:30px;padding:6px 15px;}
.search-box input{border:none;outline:none;background:none;width:100%;font-size:14px;}
.search-box i{color:#ff5722;}
#searchResults{position:absolute;top:55px;left:0;width:100%;background:white;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.2);z-index:999;overflow:hidden;}
.result-item{padding:12px 18px;cursor:pointer;font-size:14px;border-bottom:1px solid #eee;transition:0.3s;}
.result-item:hover{background:#ff9800;color:white;}
.profile{display:flex;align-items:center;cursor:pointer;}
.profile img{width:38px;height:38px;border-radius:50%;margin-left:10px;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;padding:40px;}
.card{background:white;padding:30px;border-radius:18px;text-align:center;box-shadow:0 10px 25px rgba(0,0,0,0.15);transition:0.4s;}
.card i{font-size:40px;color:#ff5722;margin-bottom:15px;}
.card:hover{transform:translateY(-12px) scale(1.04);}
.card button{background:linear-gradient(135deg,#ff9800,#ff5722);color:white;border:none;padding:10px 28px;border-radius:30px;cursor:pointer;transition:0.3s;}
.card button:hover{transform:scale(1.1);}
.train-card{background:white;padding:20px;margin:10px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.1);}
.train-card h3{margin:5px 0;}
.train-card button{padding:10px 20px;margin-top:10px;border:none;background:linear-gradient(135deg,#ff9800,#ff5722);color:white;cursor:pointer;border-radius:8px;}
.train-card button:hover{transform:scale(1.05);}
.passenger-form{margin-top:15px;}
.passenger-form input{padding:8px;margin:5px 0;width:100%;border-radius:6px;border:1px solid #ccc;}
.passenger-form button{margin-top:10px;width:100%;}
.payment-form{display:none;background:#fff;padding:20px;margin-top:15px;border-radius:12px;box-shadow:0 5px 15px rgba(0,0,0,0.2);}
.payment-form input{padding:8px;margin:5px 0;width:100%;border-radius:6px;border:1px solid #ccc;}
.payment-form button{background:linear-gradient(135deg,#ff9800,#ff5722);color:white;border:none;padding:10px 20px;border-radius:8px;width:100%;cursor:pointer;}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>🚆 Railways</h2>
    <a href="#"><i class="fa fa-home"></i> Dashboard</a>
    <a href="pnr_status.php"><i class="fa fa-search"></i> PNR Status</a>
    <a href="live_status.php"><i class="fa fa-train"></i> Live Status</a>
    <a href="my_bookings.php"><i class="fa fa-list"></i> My Bookings</a>
    <a href="#"><i class="fa fa-sign-out"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">
<header>
    <div class="search-container">
        <div class="search-box">
            <i class="fa fa-search"></i>
            <input type="text" id="search" placeholder="Search trains, routes, stations..." onkeyup="searchTrain()">
        </div>
        <div id="searchResults"></div>
    </div>
    <div class="profile">
        <span>My Profile</span>
        <img src="https://i.imgur.com/0y0y0y0.png" alt="profile">
    </div>
</header>

<div class="cards">
    <?php while($t = $trains->fetch_assoc()){ ?>
    <div class="train-card">
        <h3><?= $t['train_name'] ?> (<?= $t['train_no'] ?>)</h3>
        <p><?= $t['source'] ?> → <?= $t['destination'] ?></p>
        <p>Available Seats: <?= $t['available_seats'] ?></p>
        <p>Fare: ₹<?= $t['fare'] ?></p>
        <?php if($t['available_seats']>0){ ?>
        <button onclick="showBookingForm(<?= $t['id'] ?>, <?= $t['available_seats'] ?>, <?= $t['fare'] ?>)">Book</button>
        <?php } else { echo "<b style='color:red;'>Full</b>"; } ?>

        <!-- Passenger Info -->
        <form class="passenger-form" id="form-<?= $t['id'] ?>" method="post" style="display:none;">
            <input type="hidden" name="train_id" value="<?= $t['id'] ?>">
            <div id="passengers-<?= $t['id'] ?>"></div>
            <label>Number of seats to book:</label>
            <input type="number" min="1" max="<?= $t['available_seats'] ?>" id="seat-count-<?= $t['id'] ?>" oninput="generatePassengerFields(<?= $t['id'] ?>)">
            <button type="button" onclick="showPaymentForm(<?= $t['id'] ?>, <?= $t['fare'] ?>)">Proceed to Payment</button>
        </form>

        <!-- Payment Form -->
        <form class="payment-form" id="payment-<?= $t['id'] ?>" method="post">
            <input type="hidden" name="train_id" value="<?= $t['id'] ?>">
            <input type="hidden" name="total_fare" id="total-fare-<?= $t['id'] ?>" value="">
            <div id="payment-passengers-<?= $t['id'] ?>"></div>
            <label>Card Number</label>
            <input type="text" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX" required>
            <label>Expiry</label>
            <input type="text" name="expiry" placeholder="MM/YY" required>
            <label>CVV</label>
            <input type="text" name="cvv" placeholder="XXX" required>
            <button type="submit" name="pay">Pay Now</button>
        </form>

    </div>
    <?php } ?>
</div>
</div>

<script>
function searchTrain(){
    let q = document.getElementById("search").value;
    if(q.length < 1){
        document.getElementById("searchResults").innerHTML="";
        return;
    }
    fetch("search_trains.php?q="+q)
    .then(res => res.text())
    .then(data => {
        document.getElementById("searchResults").innerHTML = data;
    });
}

function showBookingForm(trainId, seats, fare){
    document.getElementById("form-"+trainId).style.display = "block";
    generatePassengerFields(trainId);
}

function generatePassengerFields(trainId){
    let count = document.getElementById("seat-count-"+trainId).value;
    let container = document.getElementById("passengers-"+trainId);
    container.innerHTML = "";
    for(let i=1;i<=count;i++){
        container.innerHTML += `<h4>Passenger ${i}</h4>
            <input type="text" name="passengers[]" placeholder="Full Name" required>
            <input type="text" name="aadhars[]" placeholder="Aadhar Number" required>`;
    }
}

function showPaymentForm(trainId, fare){
    let count = document.getElementById("seat-count-"+trainId).value;
    if(count < 1){
        alert("Please enter number of seats");
        return;
    }

    // Copy passenger info to payment form
    let passengerHTML = document.getElementById("passengers-"+trainId).innerHTML;
    document.getElementById("payment-passengers-"+trainId).innerHTML = passengerHTML;

    // Set total fare
    document.getElementById("total-fare-"+trainId).value = count * fare;

    // Show payment form
    document.getElementById("payment-"+trainId).style.display = "block";
}
</script>
</body>
</html>
