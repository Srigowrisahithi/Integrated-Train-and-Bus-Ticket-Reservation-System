<?php
$conn = new mysqli("localhost","root","","ticket_system");
require('fpdf.php');

if(isset($_POST['pay'])){
    $train_id = $_POST['train_id'];
    $seats = $_POST['seats'];
    $passenger_name = $_POST['passenger_name'];
    $journey_date = $_POST['journey_date'];

    // Fetch train details
    $res = $conn->query("SELECT * FROM trains WHERE id='$train_id'");
    $train = $res->fetch_assoc();

    // Calculate total
    $fare = $train['fare'];
    $total_amount = $fare * $seats;

    // Generate random PNR
    $pnr = strtoupper(substr(md5(uniqid(rand(), true)),0,10));

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (train_id, passenger_name, seats, journey_date, fare, total_amount, status, pnr) VALUES (?,?,?,?,?,?,?,?)");
    $status = 'Confirmed';
    $stmt->bind_param("isiiisss",$train_id,$passenger_name,$seats,$journey_date,$fare,$total_amount,$status,$pnr);
    $stmt->execute();

    // Reduce available seats
    $conn->query("UPDATE trains SET available_seats = available_seats - $seats WHERE id='$train_id'");

    // Generate PDF ticket
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Train Ticket',0,1,'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(50,10,'PNR:',0,0); $pdf->Cell(100,10,$pnr,0,1);
    $pdf->Cell(50,10,'Passenger Name:',0,0); $pdf->Cell(100,10,$passenger_name,0,1);
    $pdf->Cell(50,10,'Train Name:',0,0); $pdf->Cell(100,10,$train['train_name'],0,1);
    $pdf->Cell(50,10,'Train No:',0,0); $pdf->Cell(100,10,$train['train_no'],0,1);
    $pdf->Cell(50,10,'From:',0,0); $pdf->Cell(100,10,$train['source'],0,1);
    $pdf->Cell(50,10,'To:',0,0); $pdf->Cell(100,10,$train['destination'],0,1);
    $pdf->Cell(50,10,'Journey Date:',0,0); $pdf->Cell(100,10,$journey_date,0,1);
    $pdf->Cell(50,10,'Seats:',0,0); $pdf->Cell(100,10,$seats,0,1);
    $pdf->Cell(50,10,'Fare/Seat:',0,0); $pdf->Cell(100,10,'₹'.$fare,0,1);
    $pdf->Cell(50,10,'Total Amount:',0,0); $pdf->Cell(100,10,'₹'.$total_amount,0,1);

    $filename = "ticket_".$pnr.".pdf";
    $pdf->Output('D',$filename); // Force download
    exit;
}

// If user just opened payment page directly without POST
if(!isset($_POST['train_id'])){
    die("<h2 style='color:red;text-align:center;margin-top:50px;'>Please select a train first.</h2>");
}

$train_id = $_POST['train_id'];
$res = $conn->query("SELECT * FROM trains WHERE id='$train_id'");
$train = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment</title>
<style>
body{font-family:Arial;background:#f2f2f2;}
.container{width:450px;margin:50px auto;background:white;padding:20px;border-radius:12px;box-shadow:0 10px 20px rgba(0,0,0,0.2);}
h2{color:#ff5722;text-align:center;margin-bottom:20px;}
input,button{width:100%;padding:10px;margin:8px 0;border-radius:8px;border:1px solid #ccc;}
button{background:linear-gradient(135deg,#ff9800,#ff5722);color:white;border:none;cursor:pointer;}
button:hover{transform:scale(1.05);}
.info{margin:5px 0;font-weight:600;}
</style>
</head>
<body>

<div class="container">
<h2>Payment for <?= $train['train_name'] ?> (<?= $train['train_no'] ?>)</h2>
<div class="info">Fare per Seat: ₹<?= $train['fare'] ?></div>

<form method="post">
<input type="hidden" name="train_id" value="<?= $train['id'] ?>">
<input type="hidden" name="seats" value="<?= $_POST['seats'] ?>">
<input type="hidden" name="passenger_name" value="<?= $_POST['passenger_name'] ?>">
<input type="hidden" name="journey_date" value="<?= $_POST['journey_date'] ?>">

<input type="text" name="card_name" placeholder="Card Holder Name" required>
<input type="text" name="card_number" placeholder="Card Number" required>
<input type="month" name="expiry" placeholder="Expiry" required>
<input type="number" name="cvv" placeholder="CVV" required>
<button name="pay">Pay ₹<?= $train['fare'] * $_POST['seats'] ?></button>
</form>
</div>

</body>
</html>
