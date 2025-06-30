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
    $review = $_POST['review'];
    mysqli_query($conn, "UPDATE booking SET review='$review' WHERE bookingID=$bookingID");
    header("Location: current_bookings_php.php");
}
?>

<form method="post">
    Review: <textarea name="review"><?= $row['review'] ?></textarea><br>
    <input type="submit" value="Update Review">
</form>
