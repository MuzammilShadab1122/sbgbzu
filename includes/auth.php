<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_auth($require_admin = false) {
    if (!isset($_SESSION['role'])) {
        header('Location: login.php');
        exit;
    }
    
    if ($require_admin && $_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit;
    }
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function login_admin($email, $password) {
    global $db;
    if (!isset($db)) {
        require_once __DIR__ . '/db.php';
    }
    
    try {
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = ? AND `role` = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['role'] = 'admin';
            $_SESSION['user_name'] = 'Chapter Lead';
            return true;
        }
    } catch (Exception $e) {
        // Fallback protection for offline database local server instances
        if ($email === 'admin@sbg.bzu' && $password === 'awsbzu2026') {
            $_SESSION['role'] = 'admin';
            $_SESSION['user_name'] = 'Chapter Lead';
            return true;
        }
    }
    return false;
}

function login_guest() {
    $_SESSION['role'] = 'guest';
    $_SESSION['user_name'] = 'Guest Explorer';
}

function logout() {
    $_SESSION = [];
    session_destroy();
}
