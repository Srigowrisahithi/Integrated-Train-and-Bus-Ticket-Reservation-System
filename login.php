<?php
session_start();

$conn = new mysqli("localhost", "root", "", "ticket_system");

if ($conn->connect_error) {
    die("Connection failed");
}

$user = $_POST['user'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user, $user);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        echo "<script>alert('Login Successful'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid Password'); window.location='login.html';</script>";
    }
} else {
    echo "<script>alert('User not found'); window.location='login.html';</script>";
}

$conn->close();
?>
