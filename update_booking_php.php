<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $bookingID = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM booking WHERE bookingID = $bookingID");
    $row = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roomID = $_POST['roomID'];
    $customerID = $_POST['customerID'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $review = $_POST['review'];

    $update = "UPDATE booking SET roomID='$roomID', customerID='$customerID', checkin='$checkin', checkout='$checkout', review='$review' WHERE bookingID=$bookingID";
    mysqli_query($conn, $update);
    header("Location: current_bookings_php.php");
}
?>

<form method="post">
    Room ID: <input type="number" name="roomID" value="<?= $row['roomID'] ?>"><br>
    Customer ID: <input type="number" name="customerID" value="<?= $row['customerID'] ?>"><br>
    Check-in: <input type="date" name="checkin" value="<?= $row['checkin'] ?>"><br>
    Check-out: <input type="date" name="checkout" value="<?= $row['checkout'] ?>"><br>
    Review: <textarea name="review"><?= $row['review'] ?></textarea><br>
    <input type="submit" value="Update Booking">
</form>
