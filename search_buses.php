```php
<?php

$conn = new mysqli("localhost","root","","bus_ticket");

if($conn->connect_error){
die("Connection failed: ".$conn->connect_error);
}

$buses = $conn->query("SELECT * FROM buses");

?>

<!DOCTYPE html>
<html>
<head>

<title>Available Buses</title>

<style>

body{
font-family:Arial;
background:#f4f4f4;
margin:0;
padding:30px;
}

h2{
margin-bottom:25px;
}

.bus-container{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
}

.bus-card{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 4px 10px rgba(0,0,0,0.1);
text-align:center;
}

.bus-card h3{
margin-bottom:10px;
}

.bus-card p{
margin:5px 0;
color:#555;
}

button{
background:#f39c12;
border:none;
color:white;
padding:8px 15px;
border-radius:6px;
cursor:pointer;
margin-top:10px;
}

button:hover{
background:#e67e22;
}

</style>

</head>

<body>

<h2>Available Buses</h2>

<div class="bus-container">

<?php

if($buses->num_rows > 0){

while($row = $buses->fetch_assoc()){

echo "<div class='bus-card'>";

echo "<h3>".$row['bus_name']." (".$row['bus_no'].")</h3>";

echo "<p>".$row['source']." → ".$row['destination']."</p>";

echo "<p>Available Seats: ".$row['available_seats']."</p>";

echo "<p>Fare: ₹".$row['fare']."</p>";

echo "<button>Book</button>";

echo "</div>";

}

}
else{

echo "No buses available";

}

?>

</div>

</body>
</html>
```
