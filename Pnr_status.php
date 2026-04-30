<?php
$conn = new mysqli("localhost","root","","ticket_system");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$result = null;

if(isset($_POST['check'])){
    $pnr = $_POST['pnr'];

    $query = "
    SELECT b.pnr, b.passenger_name, b.aadhar_number,
           t.train_name, t.train_no, t.source, t.destination
    FROM bookings b
    JOIN trains t ON b.train_id = t.id
    WHERE b.pnr='$pnr'
    ";

    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>PNR Status</title>

<style>

body{
    font-family: Arial;
    background:#f4f4f4;
}

.container{
    width:600px;
    margin:80px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

h2{
    text-align:center;
    color:#ff5722;
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid #ccc;
    border-radius:6px;
}

button{
    width:100%;
    padding:12px;
    background:linear-gradient(135deg,#ff9800,#ff5722);
    border:none;
    color:white;
    font-size:16px;
    border-radius:6px;
    margin-top:15px;
    cursor:pointer;
}

button:hover{
    opacity:0.9;
}

table{
    width:100%;
    margin-top:25px;
    border-collapse:collapse;
}

table th, table td{
    padding:10px;
    border:1px solid #ddd;
    text-align:center;
}

table th{
    background:#ff5722;
    color:white;
}

</style>
</head>

<body>

<div class="container">

<h2>Check PNR Status</h2>

<form method="post">
<input type="text" name="pnr" placeholder="Enter PNR Number" required>
<button name="check">Check Status</button>
</form>

<?php
if($result && $result->num_rows>0){
?>

<table>

<tr>
<th>PNR</th>
<th>Passenger</th>
<th>Aadhar</th>
<th>Train</th>
<th>Route</th>
</tr>

<?php
while($row = $result->fetch_assoc()){
?>

<tr>
<td><?php echo $row['pnr']; ?></td>
<td><?php echo $row['passenger_name']; ?></td>
<td><?php echo $row['aadhar_number']; ?></td>
<td><?php echo $row['train_name']." (".$row['train_no'].")"; ?></td>
<td><?php echo $row['source']." → ".$row['destination']; ?></td>
</tr>

<?php } ?>

</table>

<?php
}else if(isset($_POST['check'])){
    echo "<p style='color:red;text-align:center;margin-top:20px;'>PNR Not Found</p>";
}
?>

</div>

</body>
</html>