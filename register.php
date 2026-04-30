<?php
// PHP Code for Registration
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $mobile    = trim($_POST['mobile']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if user exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=? OR username=?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email or Username already exists!";
    } else {
        $sql = "INSERT INTO users (full_name, email, mobile, username, password)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $full_name, $email, $mobile, $username, $hashed_password);

        if ($stmt->execute()) {
            $message = "Registration Successful! <a href='login.php'>Login here</a>";
        } else {
            $message = "Registration Failed!";
        }
        $stmt->close();
    }
    $check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
        }

        header, footer {
            background-color: orange;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }

        .container {
            width: 420px;
            margin: 40px auto;
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 6px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #999;
            border-radius: 4px;
        }

        button {
            width: 100%;
            background-color: orange;
            color: white;
            padding: 10px;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: darkorange;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: orange;
            text-decoration: none;
            font-weight: bold;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: red;
        }
    </style>
</head>
<body>

<header>Ticket Reservation System</header>

<div class="container">
    <h2>Registration Form</h2>

    <?php if($message != "") { echo "<div class='message'>$message</div>"; } ?>

    <form action="" method="post">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="mobile" placeholder="Mobile Number" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        Already a user? <a href="login.php">Login here</a>
    </div>
</div>

<footer>© 2026 Ticket Reservation System</footer>

</body>
</html>
