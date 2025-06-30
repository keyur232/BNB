<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        try {
            // For simplicity, using basic authentication
            // In production, use proper password hashing
            $stmt = $pdo->prepare("SELECT id, username FROM customers WHERE username = ? AND password = ?");
            $stmt->execute([$username, $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: current_bookings.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch(PDOException $e) {
            $error = 'Login failed. Please try again.';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ongaonga B&B</title>
</head>
<body>
    <h2>Login to Ongaonga Bed & Breakfast</h2>
    
    <?php if ($error): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required style="width: 200px; padding: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required style="width: 200px; padding: 5px;">
        </div>
        
        <div>
            <input type="submit" value="Login" style="padding: 8px 20px; background-color: #4CAF50; color: white; border: none;">
        </div>
    </form>
    
    <p style="margin-top: 20px; font-size: 12px; color: #666;">
        Demo credentials - Username: admin, Password: admin
    </p>
</body>
</html>