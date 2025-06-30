<?php
require_once 'config.php';
checkLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: current_bookings.php');
    exit();
}

$booking_id = (int)$_GET['id'];
$error = '';
$success = '';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM booking WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Booking deleted successfully!';
            header('Location: current_bookings.php');
            exit();
        } else {
            $error = 'Booking not found or already deleted.';
        }
    } catch(PDOException $e) {
        $error = 'Error deleting booking: ' . $e->getMessage();
    }
}

// Fetch booking details for preview
try {
    $stmt = $pdo->prepare("
        SELECT b.id, b.checkin_date, b.checkout_date, b.contact_number, b.extras,
               r.roomname, c.firstname, c.lastname
        FROM booking b
        JOIN room r ON b.roomID = r.roomID
        JOIN customers c ON b.customerID = c.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        header('Location: current_bookings.php');
        exit();
    }
} catch(PDOException $e) {
    $error = "Error fetching booking: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Preview Before Deletion</title>
</head>
<body>
    <h2>Booking preview before deletion</h2>
    
    <a href="current_bookings.php" style="color: blue;">[Return to the booking listing]</a>
    <a href="current_bookings.php" style="color: blue;">[Return to the main page]</a>
    
    <br><br>
    
    <?php if ($error): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($booking) && !$error): ?>
        <fieldset>
            <legend><strong>Booking detail #<?php echo htmlspecialchars($booking['id']); ?></strong></legend>
            <strong>Customer:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?><br>
            <strong>Room name:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['roomname']); ?><br>
            <strong>Checkin date:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['checkin_date']); ?><br>
            <strong>Checkout date:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['checkout_date']); ?><br>
        </fieldset>
        
        <br>
        
        <strong>Are you sure you want to delete this Booking?</strong>
        
        <br><br>
        
        <form method="POST" action="">
            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
            <input type="hidden" name="confirm_delete" value="1">
            <button type="submit" style="background-color: #ddd; border: 1px solid #999; padding: 2px 8px;" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
            <a href="current_bookings.php" style="color: blue;">[Cancel]</a>
        </form>
    <?php endif; ?>
</body>
</html>