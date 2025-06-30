<?php
include 'config.php';

$from = $_GET['from'];
$to = $_GET['to'];

$sql = "SELECT * FROM room WHERE roomID NOT IN (
    SELECT roomID FROM booking WHERE 
    (checkin <= '$to' AND checkout >= '$from')
)";

$result = mysqli_query($conn, $sql);
$rooms = [];

while ($row = mysqli_fetch_assoc($result)) {
    $rooms[] = $row;
}

echo json_encode($rooms);
?>
