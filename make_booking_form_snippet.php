<form method="post" action="save_booking.php">
    Customer ID: <input type="number" name="customerID" required><br>
    Room:
    <select name="roomID">
        <?php
        $rooms = mysqli_query($conn, "SELECT * FROM room");
        while ($room = mysqli_fetch_assoc($rooms)) {
            echo "<option value='{$room['roomID']}'>{$room['roomName']}</option>";
        }
        ?>
    </select><br>
    Check-in: <input type="date" name="checkin" required><br>
    Check-out: <input type="date" name="checkout" required><br>
    <input type="submit" value="Book Now">
</form>
