<?php

$conn = new mysqli("localhost","root","","ticket_system");

$pnr = $_GET['pnr'];

$query = "
SELECT b.pnr,b.passenger_name,b.aadhar_number,
t.train_name,t.train_no,t.source,t.destination
FROM bookings b
JOIN trains t ON b.train_id=t.id
WHERE b.pnr='$pnr'
";

$result = $conn->query($query);

?>

<html>
<head>

<title>Train Ticket</title>

<style>

body{
font-family:Arial;
background:#f4f4f4;
}

.ticket{

width:500px;
margin:50px auto;
padding:30px;
background:white;
border-radius:10px;
box-shadow:0 5px 20px rgba(0,0,0,0.2);

}

h2{
text-align:center;
color:#ff5722;
}

p{
margin:10px 0;
}

button{

padding:10px 20px;
background:#ff5722;
border:none;
color:white;
cursor:pointer;

}

</style>

</head>

<body>

<div class="ticket">

<h2>🚆 Railway Ticket</h2>

<?php

while($row = $result->fetch_assoc()){

echo "<p><b>PNR:</b> ".$row['pnr']."</p>";
echo "<p><b>Passenger:</b> ".$row['passenger_name']."</p>";
echo "<p><b>Aadhar:</b> ".$row['aadhar_number']."</p>";
echo "<p><b>Train:</b> ".$row['train_name']." (".$row['train_no'].")</p>";
echo "<p><b>Route:</b> ".$row['source']." → ".$row['destination']."</p>";
echo "<hr>";

}

?>

<button onclick="window.print()">Print Ticket</button>

</div>

</body>
</html>