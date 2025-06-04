<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $conn->real_escape_string($_POST['token']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Verify token
    $result = $conn->query("SELECT email FROM password_resets 
                          WHERE token = '$token' 
                          AND expires_at > NOW() 
                          AND used = 0");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        
        // Update password
        $conn->query("UPDATE users SET password = '$password' WHERE email = '$email'");
        
        // Mark token as used
        $conn->query("UPDATE password_resets SET used = 1 WHERE token = '$token'");
        
        echo "Password updated successfully!<br>
              <a href='login.html'>Login with new password</a>";
    } else {
        echo "Invalid or expired token";
    }
} else {
    // Display reset form
    $token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';
    echo '<form method="POST">
            <h2>Reset Password</h2>
            <input type="hidden" name="token" value="'.$token.'">
            <label>New Password: <input type="password" name="password" required></label><br>
            <label>Confirm Password: <input type="password" name="confirm_password" required></label><br>
            <button type="submit">Reset Password</button>
          </form>';
}