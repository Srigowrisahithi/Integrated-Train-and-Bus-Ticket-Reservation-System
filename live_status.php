<?php
session_start();

$conn = new mysqli("localhost","root","","bus_ticket");

if($conn->connect_error){
die("Connection failed: ".$conn->connect_error);
}

$status = null;

if(isset($_POST['search'])){

$bus_no = $_POST['bus_no'];

$query = "SELECT * FROM buses WHERE bus_no='$bus_no'";

$result = $conn->query($query);

if($result->num_rows > 0){
$status = $result->fetch_assoc();
}else{
echo "<script>alert('Bus not found');</script>";
}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Live Bus Status</title>

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

/* SIDEBAR */

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
margin-bottom:30px;
}

.sidebar a{
display:flex;
align-items:center;
padding:15px 25px;
color:white;
text-decoration:none;
transition:0.3s;
}

.sidebar a:hover{
background:rgba(255,255,255,0.2);
padding-left:35px;
}

/* MAIN */

.main{
margin-left:240px;
width:100%;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}

/* CARD */

.container{
width:420px;
background:white;
padding:35px;
border-radius:12px;
box-shadow:0 8px 20px rgba(0,0,0,0.1);
text-align:center;
}

/* TITLE */

h2{
margin-bottom:20px;
color:#ff5722;
}

/* INPUT */

input{
width:100%;
padding:12px;
margin:12px 0;
border-radius:8px;
border:1px solid #ccc;
}

/* BUTTON */

button{
width:100%;
padding:12px;
background:linear-gradient(135deg,#ff9800,#ff5722);
color:white;
border:none;
border-radius:8px;
cursor:pointer;
}

button:hover{
opacity:0.9;
}

/* STATUS BOX */

.status-box{
margin-top:20px;
background:#fff3e0;
padding:18px;
border-radius:10px;
text-align:left;
}

.status-box h3{
color:#ff5722;
margin-bottom:10px;
}

.status-box p{
margin:6px 0;
}

.status-box b{
color:#ff5722;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<h2>🎫 Dashboard</h2>

<a href="dashboard.php">🏠 Dashboard</a>

<a href="bus_dashboard.php">🚌 Book Bus</a>

<a href="train_dashboard.php">🚆 Book Train</a>

<a href="live_status.php">📡 Live Status</a>

<a href="my_bookings.php">📜 My Bookings</a>

<a href="cancel_ticket.php">❌ Cancel Ticket</a>

<a href="print_ticket.php">🧾 Print Ticket</a>

<a href="logout.php">🚪 Logout</a>

</div>

<!-- MAIN -->

<div class="main">

<div class="container">

<h2>Live Bus Status</h2>

<form method="post">

<input type="text" name="bus_no" placeholder="Enter Bus Number" required>

<button name="search">Check Status</button>

</form>

<?php if($status){ ?>

<div class="status-box">

<h3><?= $status['bus_name'] ?> (<?= $status['bus_no'] ?>)</h3>

<p><b>Route:</b> <?= $status['source'] ?> → <?= $status['destination'] ?></p>

<p><b>Status:</b> Running On Time</p>

<p><b>Next Stop:</b> <?= $status['destination'] ?></p>

<p><b>Seats Available:</b> <?= $status['available_seats'] ?></p>

</div>

<?php } ?>

</div>

</div>

</body>

</html>