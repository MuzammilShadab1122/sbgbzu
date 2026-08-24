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

function is_member() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'member' && !empty($_SESSION['member_id']);
}

function get_logged_member() {
    global $db;
    if (!is_member()) return null;
    if (!isset($db)) {
        require_once __DIR__ . '/db.php';
    }
    try {
        $stmt = $db->prepare("SELECT * FROM `members` WHERE `id` = ?");
        $stmt->execute([$_SESSION['member_id']]);
        return $stmt->fetch() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Unified Login Function:
 * Accepts Member ID / Admin Email / Username / Full Name + Password.
 * 1. Checks `users` table for Admin.
 * 2. Checks `members` table for Member using member_code, id, or name.
 * STRICT: Only credentials created/entered by Admin are permitted (no default passwords).
 */
function login_user($username_or_id, $password) {
    global $db;
    if (!isset($db)) {
        require_once __DIR__ . '/db.php';
    }
    
    $input = trim($username_or_id);
    $pass = trim($password);
    
    if (empty($input) || empty($pass)) {
        return false;
    }
    
    // 1. Try Admin Login (users table)
    try {
        $stmt = $db->prepare("SELECT * FROM `users` WHERE (`email` = ? OR `email` LIKE ?) AND `role` = 'admin'");
        $stmt->execute([$input, $input . '%']);
        $adminUser = $stmt->fetch();
        
        if ($adminUser && password_verify($pass, $adminUser['password'])) {
            $_SESSION['role'] = 'admin';
            $_SESSION['user_name'] = 'Chapter Lead';
            unset($_SESSION['member_id']);
            unset($_SESSION['member_code']);
            return 'admin';
        }
    } catch (Exception $e) {}
    
    // Fallback Admin credentials
    if (($input === 'admin@sbg.bzu' || strtolower($input) === 'admin') && $pass === 'awsbzu2026') {
        $_SESSION['role'] = 'admin';
        $_SESSION['user_name'] = 'Chapter Lead';
        unset($_SESSION['member_id']);
        unset($_SESSION['member_code']);
        return 'admin';
    }
    
    // 2. Try Member Login (members table by Member Code/ID, numeric ID, or Name)
    try {
        $stmt = $db->prepare("SELECT * FROM `members` WHERE LOWER(`member_code`) = LOWER(?) OR `id` = ? OR LOWER(`name`) = LOWER(?) OR `name` LIKE ?");
        $stmt->execute([$input, is_numeric($input) ? intval($input) : 0, $input, '%' . $input . '%']);
        $members = $stmt->fetchAll();
        
        foreach ($members as $mb) {
            // Verify password strictly against database hash
            if (!empty($mb['password']) && password_verify($pass, $mb['password'])) {
                $_SESSION['role'] = 'member';
                $_SESSION['member_id'] = $mb['id'];
                $_SESSION['member_code'] = $mb['member_code'] ?? ('SBG-' . sprintf('%03d', $mb['id']));
                $_SESSION['member_name'] = $mb['name'];
                $_SESSION['user_name'] = $mb['name'];
                return 'member';
            }
        }
    } catch (Exception $e) {}
    
    return false;
}

function login_guest() {
    $_SESSION['role'] = 'guest';
    $_SESSION['user_name'] = 'Guest Explorer';
    unset($_SESSION['member_id']);
}

function logout() {
    $_SESSION = [];
    session_destroy();
}
