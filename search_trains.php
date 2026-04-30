<?php
$conn = new mysqli("localhost","root","","ticket_system");
if($conn->connect_error) die("Connection failed");

$q = $_GET['q'];

$sql = "SELECT * FROM trains 
        WHERE train_name LIKE '%$q%' 
        OR train_no LIKE '%$q%' 
        OR source LIKE '%$q%' 
        OR destination LIKE '%$q%'";

$result = $conn->query($sql);

if($result->num_rows>0){

while($row = $result->fetch_assoc()){

echo "<div class='result-item'>
<b>".$row['train_name']." (".$row['train_no'].")</b><br>
".$row['source']." → ".$row['destination']."
</div>";

}

}else{
echo "<div class='result-item'>No trains found</div>";
}
?>
