<?php
session_start();

$conn = new mysqli("localhost","root","","bus_ticket");

$ticket=null;

if(isset($_POST['search'])){

$pnr=$_POST['pnr'];

$query="
SELECT b.bus_name,b.source,b.destination,
bb.passenger_name,bb.aadhar_number,
bb.pnr,bb.travel_date
FROM bus_bookings bb
JOIN buses b ON bb.bus_id=b.id
WHERE bb.pnr='$pnr'
";

$result=$conn->query($query);

if($result->num_rows>0){
$ticket=$result->fetch_assoc();
}else{
echo "<script>alert('Invalid PNR');</script>";
}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Print Ticket</title>

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
width:400px;
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

/* TICKET */

.ticket{
margin-top:20px;
background:#fff3e0;
padding:18px;
border-radius:10px;
text-align:left;
}

.ticket b{
color:#ff5722;
}

.print-btn{
margin-top:10px;
background:#333;
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

<h2>Print Ticket</h2>

<form method="post">

<input type="text" name="pnr" placeholder="Enter PNR Number" required>

<button name="search">Search Ticket</button>

</form>

<?php if($ticket){ ?>

<div class="ticket">

<p><b>Passenger:</b> <?= $ticket['passenger_name'] ?></p>

<p><b>Bus:</b> <?= $ticket['bus_name'] ?></p>

<p><b>Route:</b> <?= $ticket['source'] ?> → <?= $ticket['destination'] ?></p>

<p><b>Travel Date:</b> <?= date("d-m-Y",strtotime($ticket['travel_date'])) ?></p>

<p><b>PNR:</b> <?= $ticket['pnr'] ?></p>

<br>

<button class="print-btn" onclick="window.print()">Print Ticket</button>

</div>

<?php } ?>

</div>

</div>

</body>
</html>