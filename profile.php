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

$sql = "SELECT full_name, email, mobile, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>

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
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 6px 0 12px;
            border: 1px solid #999;
            border-radius: 4px;
        }

        textarea {
            resize: none;
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

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: orange;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

<header>Ticket Reservation System</header>

<div class="container">
    <h2>My Profile</h2>

    <form action="update_profile.php" method="post">
        <label>Name</label>
        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>

        <label>Email</label>
        <input type="email" value="<?php echo $user['email']; ?>" readonly>

        <label>Phone</label>
        <input type="text" name="mobile" value="<?php echo $user['mobile']; ?>" required>

        <label>Address (Optional)</label>
        <textarea name="address" rows="3"><?php echo $user['address']; ?></textarea>

        <button type="submit">Update Profile</button>
    </form>

    <div class="back-link">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

<footer>© 2026 Ticket Reservation System</footer>

</body>
</html>

