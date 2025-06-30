<?php
require_once 'config.php';
checkLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: current_bookings.php');
    exit();
}

$booking_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT b.id, b.checkin_date, b.checkout_date, b.contact_number, b.extras, b.review,
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
    <title>Booking Details View</title>
</head>
<body>
    <h2>Booking Details View</h2>
    
    <a href="current_bookings.php" style="color: blue;">[Return to the booking listing]</a>
    <a href="current_bookings.php" style="color: blue;">[Return to the main page]</a>
    
    <br><br>
    
    <?php if (isset($error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php else: ?>
        <fieldset>
            <legend>Room detail #<?php echo htmlspecialchars($booking['id']); ?></legend>
            <strong>Customer:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?><br>
            <strong>Room name:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['roomname']); ?><br>
            <strong>Checkin date:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['checkin_date']); ?><br>
            <strong>Checkout date:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['checkout_date']); ?><br>
            <strong>Contact number:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['contact_number']); ?><br>
            <strong>Extras:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['extras'] ?: 'nothing'); ?><br>
            <strong>Room review:</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($booking['review'] ?: 'nothing'); ?><br>
        </fieldset>
        
        <br>
        
        <button type="button" style="background-color: #ddd; border: 1px solid #999; padding: 2px 8px;" onclick="window.location.href='edit_booking.php?id=<?php echo $booking['id']; ?>'">Edit</button>
    <?php endif; ?>
</body>
</html>