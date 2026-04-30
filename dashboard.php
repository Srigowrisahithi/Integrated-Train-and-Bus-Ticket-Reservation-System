<?php
// Database connection
$conn = new mysqli("localhost","root","","ticket_system");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Example: fetch wallet balance (replace with actual user session)
$wallet_balance = 1250.00;

// Example: fetch recent bookings
$recent_bookings = $conn->query("
SELECT b.pnr, b.passenger_name, t.train_name, t.source, t.destination
FROM bookings b
JOIN trains t ON b.train_id = t.id
ORDER BY b.id DESC
LIMIT 3
");

?>

<!DOCTYPE html>
<html>
<head>
<title>Ticket Reservation System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI;}
body{display:flex;min-height:100vh;background:#f4f4f4;}
/* Sidebar */
.sidebar{width:220px;background:#1e1e1e;color:white;flex-shrink:0;display:flex;flex-direction:column;padding-top:30px;}
.sidebar h2{text-align:center;margin-bottom:30px;font-size:20px;}
.sidebar a{color:white;text-decoration:none;padding:15px 20px;display:block;transition:0.3s;}
.sidebar a:hover{background:#ff9800;color:white;}
/* Main content */
.main{flex:1;padding:20px;}
header{display:flex;justify-content:space-between;align-items:center;background:#ff9800;padding:15px 30px;border-radius:10px;margin-bottom:20px;}
header input{padding:8px 12px;width:300px;border-radius:6px;border:none;}
header .profile{width:40px;height:40px;border-radius:50%;background:#fff;text-align:center;line-height:40px;color:#ff9800;font-weight:bold;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:20px;}
.card{background:white;padding:25px;border-radius:12px;box-shadow:0 5px 20px rgba(0,0,0,0.1);text-align:center;transition:0.3s;}
.card:hover{transform:translateY(-5px);}
.card h3{margin-bottom:10px;}
.card p{margin-bottom:15px;color:#555;}
.card button{background:#ff9800;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;transition:0.3s;}
.card button:hover{opacity:0.9;}
/* Sub-cards */
.sub-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.sub-card{background:white;padding:20px;border-radius:12px;box-shadow:0 5px 15px rgba(0,0,0,0.1);text-align:center;}
.sub-card h4{margin-bottom:10px;color:#ff5722;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
<h2>Ticket Reservation</h2>
<a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="my_bookings.php"><i class="fa fa-list"></i> My Bookings</a>
<a href="#"><i class="fa fa-wallet"></i> Wallet</a>
<a href="#"><i class="fa fa-headset"></i> Support</a>
<a href="#"><i class="fa fa-sign-out"></i> Logout</a>
</div>

<!-- Main -->
<div class="main">

<header>
<input type="text" placeholder="Search Train / Bus / PNR">
<div class="profile">R</div>
</header>

<h2>Welcome, rupa@12 👋</h2>

<div class="cards">
<!-- Railway Reservation -->
<div class="card">
<i class="fa fa-train fa-2x"></i>
<h3>Railway Reservation</h3>
<p>Book train tickets, check PNR, live status.</p>
<button onclick="window.location.href='railway_dashboard.php'">Book Train</button>
</div>

<!-- Bus Reservation -->
<div class="card">
<i class="fa fa-bus fa-2x" style="color:red;"></i>
<h3>Bus Reservation</h3>
<p>Book buses, seat selection, live tracking.</p>
<button onclick="window.location.href='book_bus.php'">Book Bus</button>
</div>
</div>

<!-- Sub info cards -->
<div class="sub-cards">
<div class="sub-card">
<h4>Wallet Balance</h4>
<p>₹ <?= number_format($wallet_balance,2) ?></p>
</div>
<div class="sub-card">
<h4>Upcoming Journeys</h4>
<p>No journeys scheduled</p>
</div>
<div class="sub-card">
<h4>Recent Bookings</h4>
<?php
if($recent_bookings->num_rows>0){
    while($b = $recent_bookings->fetch_assoc()){
        echo "<p>".$b['train_name']." (".$b['source']."→".$b['destination'].")<br>PNR: ".$b['pnr']."</p>";
    }
}else{
    echo "<p>No recent bookings</p>";
}
?>
</div>
</div>

</div>
</body>
</html>