<?php
session_start();

// Limit to 3 requests per 10 minutes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['reset_attempts'])) {
        $_SESSION['reset_attempts'] = 1;
        $_SESSION['first_attempt'] = time();
    } else {
        if (time() - $_SESSION['first_attempt'] > 600) { // 10 minutes
            $_SESSION['reset_attempts'] = 1;
            $_SESSION['first_attempt'] = time();
        } else {
            $_SESSION['reset_attempts']++;
        }
    }
    
    if ($_SESSION['reset_attempts'] > 3) {
        die("Too many attempts. Try again later.");
    }
}