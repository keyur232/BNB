<?php
require_once 'config.php';
checkLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: current_bookings.php');
    exit();
}

$booking_id = (int)$_GET['id'];
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $review = sanitizeInput($_POST['room_review']);
    
    if (empty($review)) {
        $error = 'Please enter a review.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE booking SET review = ? WHERE id = ?");
            $stmt->execute([$review, $booking_id]);
            
            $_SESSION['message'] = 'Review updated successfully!';
            header('Location: current_bookings.php');
            exit();
        } catch(PDOException $e) {
            $error = 'Error updating review: ' . $e->getMessage();
        }
    }
}

// Fetch booking details
try {
    $stmt = $pdo->prepare("
        SELECT b.id, b.review, b.checkin_date, b.checkout_date,
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
    
    // Check if booking has already taken place (past checkout date)
    $today = date('Y-m-d');
    if ($booking['checkout_date'] > $today) {
        $error = 'Reviews can only be added after the checkout date.';
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
    <title>Edit/Add Room Review</title>
</head>
<body>
    <h2>Edit/add room review</h2>
    
    <a href="current_bookings.php" style="color: blue;">[Return to the booking listing]</a>
    <a href="current_bookings.php" style="color: blue;">[Return to the main page]</a>
    
    <br><br>
    
    <?php if ($error): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($booking) && !$error): ?>
        <strong>Review made by <?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></strong>
        
        <br><br>
        
        <form method="POST" action="">
            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
            
            <label for="room_review">Room review:</label><br>
            <textarea id="room_review" name="room_review" rows="8" cols="50" required><?php echo htmlspecialchars($booking['review'] ?: ''); ?></textarea>
            
            <br><br>
            
            <button type="submit" style="background-color: #ddd; border: 1px solid #999; padding: 2px 8px;">Update</button>
            <a href="current_bookings.php" style="color: blue;">[Cancel]</a>
        </form>
    <?php endif; ?>
</body>
</html>