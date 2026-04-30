<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) {
    die("Connection failed");
}

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$mobile = $_POST['mobile'];
$address = $_POST['address'];

$sql = "UPDATE users SET full_name=?, mobile=?, address=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $full_name, $mobile, $address, $user_id);

if ($stmt->execute()) {
    echo "<script>alert('Profile Updated Successfully'); window.location='profile.php';</script>";
} else {
    echo "<script>alert('Update Failed'); window.location='profile.php';</script>";
}

$conn->close();
?>

