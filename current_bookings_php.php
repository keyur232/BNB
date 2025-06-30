<?php
require_once 'config.php';
checkLogin();

try {
    // Fetch current bookings with room information
    $stmt = $pdo->prepare("
        SELECT b.id, b.checkin_date, b.checkout_date, b.contact_number, b.extras, b.review,
               r.roomname, c.firstname, c.lastname
        FROM booking b
        JOIN room r ON b.roomID = r.roomID
        JOIN customers c ON b.customerID = c.id
        ORDER BY b.checkin_date DESC
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching bookings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Bookings</title>
</head>
<body>
    <h2>Current bookings</h2>
    
    <a href="make_booking.php" style="color: blue;">[Make a booking]</a>
    <a href="logout.php" style="color: blue;">[Logout]</a>
    
    <br><br>
    
    <?php if (isset($error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Room Name</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($booking['roomname']); ?></td>
                    <td><?php echo htmlspecialchars($booking['checkin_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['checkout_date']); ?></td>
                    <td>
                        <a href="view_booking.php?id=<?php echo $booking['id']; ?>" style="color: blue;">[View]</a>
                        <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" style="color: blue;">[Edit]</a>
                        <a href="delete_booking.php?id=<?php echo $booking['id']; ?>" style="color: blue;">[Delete]</a>
                        <a href="edit_review.php?id=<?php echo $booking['id']; ?>" style="color: blue;">[Review]</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No bookings found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>