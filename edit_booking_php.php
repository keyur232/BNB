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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = sanitizeInput($_POST['room_id']);
    $checkin_date = sanitizeInput($_POST['checkin_date']);
    $checkout_date = sanitizeInput($_POST['checkout_date']);
    $contact_number = sanitizeInput($_POST['contact_number']);
    $extras = sanitizeInput($_POST['booking_extras']);
    
    // Validate input
    if (empty($room_id) || empty($checkin_date) || empty($checkout_date) || empty($contact_number)) {
        $error = 'Please fill in all required fields.';
    } elseif (!validateDate($checkin_date) || !validateDate($checkout_date)) {
        $error = 'Please enter valid dates in YYYY-MM-DD format.';
    } elseif ($checkin_date >= $checkout_date) {
        $error = 'Check-out date must be after check-in date.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE booking 
                SET roomID = ?, checkin_date = ?, checkout_date = ?, contact_number = ?, extras = ?
                WHERE id = ?
            ");
            $stmt->execute([$room_id, $checkin_date, $checkout_date, $contact_number, $extras, $booking_id]);
            
            $_SESSION['message'] = 'Booking updated successfully!';
            header('Location: current_bookings.php');
            exit();
        } catch(PDOException $e) {
            $error = 'Error updating booking: ' . $e->getMessage();
        }
    }
}

// Fetch current booking details
try {
    $stmt = $pdo->prepare("
        SELECT b.*, r.roomname, c.firstname, c.lastname
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
    
    // Fetch all rooms for dropdown
    $stmt = $pdo->prepare("SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomname");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a Booking</title>
</head>
<body>
    <h2>Edit a booking</h2>
    
    <a href="current_bookings.php" style="color: blue;">[Return to the Bookings listing]</a>
    <a href="current_bookings.php" style="color: blue;">[Return to the main page]</a>
    
    <br><br>
    
    <?php if ($error): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($booking)): ?>
        <strong>Booking made for <?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></strong>
        
        <br><br>
        
        <form method="POST" action="">
            <label for="room_id">Room (name,type,beds):</label>
            <select id="room_id" name="room_id" required>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo $room['roomID']; ?>" 
                            <?php echo ($room['roomID'] == $booking['roomID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($room['roomname'] . ', ' . $room['roomtype'] . ', ' . $room['beds']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <br><br>
            
            <label for="checkin_date">Checkin date:</label>
            <input type="date" id="checkin_date" name="checkin_date" 
                   value="<?php echo htmlspecialchars($booking['checkin_date']); ?>" required>
            
            <br><br>
            
            <label for="checkout_date">Checkout date:</label>
            <input type="date" id="checkout_date" name="checkout_date" 
                   value="<?php echo htmlspecialchars($booking['checkout_date']); ?>" required>
            
            <br><br>
            
            <label for="contact_number">Contact number:</label>
            <input type="tel" id="contact_number" name="contact_number" 
                   value="<?php echo htmlspecialchars($booking['contact_number']); ?>" required>
            
            <br><br>
            
            <label for="booking_extras">Booking extras:</label><br>
            <textarea id="booking_extras" name="booking_extras" rows="5" cols="50"><?php echo htmlspecialchars($booking['extras']); ?></textarea>
            
            <br><br>
            
            <input type="submit" value="Update">
            <a href="current_bookings.php" style="color: blue;">[Cancel]</a>
        </form>
    <?php endif; ?>
</body>
</html>