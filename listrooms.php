<!DOCTYPE HTML>
<html>
<head>
    <title>Browse Rooms</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: auto; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        h2 { text-align: center; }
    </style>
</head>
<body>

<?php
include("db.php");

// Fetch all rooms
$query = "SELECT * FROM room";
$result = mysqli_query($conn, $query);

echo "<h2>Room Listings</h2>";

if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Room ID</th><th>Room Type</th><th>Beds</th><th>Price</th><th>Actions</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['roomID'];
        echo "<tr>";
        echo "<td>{$row['roomID']}</td>";
        echo "<td>{$row['roomtype']}</td>";
        echo "<td>{$row['beds']}</td>";
        echo "<td>{$row['price']}</td>";
        echo "<td>
                <a href='viewroom.php?id={$id}'>[view]</a>
                <a href='editroom.php?id={$id}'>[edit]</a>
                <a href='deleteroom.php?id={$id}'>[delete]</a>
              </td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<h2>No rooms found!</h2>";
}

mysqli_free_result($result);
mysqli_close($conn);
?>

</body>
</html>
