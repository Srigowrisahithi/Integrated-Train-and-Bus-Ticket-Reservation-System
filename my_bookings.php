<?php
$conn = new mysqli("localhost","root","","ticket_system");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Fetch bookings (include booking id for cancel)
$query = "
SELECT b.id, b.pnr, b.passenger_name, b.aadhar_number,
       t.train_name, t.train_no, t.source, t.destination
FROM bookings b
JOIN trains t ON b.train_id = t.id
ORDER BY b.id DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Bookings</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI;}
body{background:#f2f2f2;}
.container{width:90%;margin:40px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.15);}
h2{text-align:center;margin-bottom:20px;color:#ff5722;}
table{width:100%;border-collapse:collapse;}
table th, table td{padding:12px;border:1px solid #ddd;text-align:center;}
table th{background:#ff5722;color:white;}
tr:nth-child(even){background:#f9f9f9;}
.back{margin-bottom:20px;}
.back a{text-decoration:none;background:linear-gradient(135deg,#ff9800,#ff5722);color:white;padding:10px 18px;border-radius:6px;}
.back a:hover{opacity:0.9;}
a.button{padding:5px 12px;background:#ff5722;color:white;border-radius:5px;text-decoration:none;}
a.button:hover{opacity:0.8;}
</style>
</head>

<body>

<div class="container">

<div class="back">
<a href="railway_dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2>My Booking List</h2>

<table>
<tr>
<th>PNR</th>
<th>Passenger Name</th>
<th>Aadhar</th>
<th>Train</th>
<th>Route</th>
<th>Print</th>
<th>Cancel</th>
</tr>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
?>
<tr>
<td><?php echo $row['pnr']; ?></td>
<td><?php echo $row['passenger_name']; ?></td>
<td><?php echo $row['aadhar_number']; ?></td>
<td><?php echo $row['train_name']." (".$row['train_no'].")"; ?></td>
<td><?php echo $row['source']." → ".$row['destination']; ?></td>
<td>
<a class="button" href="ticket_download.php?pnr=<?php echo $row['pnr']; ?>">Print</a>
</td>
<td>
<a class="button" href="cancel_ticket.php?id=<?php echo $row['id']; ?>" style="background:red;">Cancel</a>
</td>
</tr>
<?php
    }
}else{
    echo "<tr><td colspan='7'>No bookings found</td></tr>";
}
?>

</table>

</div>

</body>
</html>