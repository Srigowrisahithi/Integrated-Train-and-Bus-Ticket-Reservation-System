<?php
$conn = new mysqli("localhost","root","","bus_ticket");

if ($conn->connect_error) {
    die("Database Connection Failed");
}

if (isset($_POST['add_bus'])) {

    $bus_name    = $_POST['bus_name'];
    $source      = $_POST['source'];
    $destination = $_POST['destination'];
    $seats       = $_POST['seats'];
    $fare        = $_POST['fare'];

    $stmt = $conn->prepare(
        "INSERT INTO buses
        (bus_name, source, destination, total_seats, available_seats, fare)
        VALUES (?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "sssiii",
        $bus_name,
        $source,
        $destination,
        $seats,
        $seats,
        $fare
    );

    $stmt->execute();

    echo "<script>alert('Bus Added Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin – Add Bus</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#f2f2f2;
}

.form-box{
    width:420px;
    background:white;
    margin:60px auto;
    padding:30px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

.form-box h2{
    text-align:center;
    margin-bottom:20px;
    color:#2196f3;
}

.form-box input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
}

.form-box button{
    width:100%;
    background:linear-gradient(135deg,#2196f3,#1976d2);
    color:white;
    border:none;
    padding:12px;
    margin-top:10px;
    border-radius:30px;
    cursor:pointer;
    font-size:16px;
}

.form-box button:hover{
    transform:scale(1.05);
}
</style>
</head>

<body>

<div class="form-box">
    <h2>🚌 Add New Bus</h2>

    <form method="post">
        <input name="bus_name" placeholder="Bus Name" required>
        <input name="source" placeholder="Source City" required>
        <input name="destination" placeholder="Destination City" required>
        <input type="number" name="seats" placeholder="Total Seats" required>
        <input type="number" name="fare" placeholder="Fare (₹)" required>
        <button name="add_bus">Add Bus</button>
    </form>
</div>

</body>
</html>
