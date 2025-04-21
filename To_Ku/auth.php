<?php
// auth.php - Handles login, register and logout

session_start();
require_once "db.php";

// Login process
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        header("Location: index.php?error=Username and password are required");
        exit();
    }
    
    // Retrieve user from database
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: index.php?error=Invalid username or password");
            exit();
        }
    } else {
        header("Location: index.php?error=Invalid username or password");
        exit();
    }
}

// Registration process
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=All fields are required");
        exit();
    }
    
    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit();
    }
    
    // Check if username or email already exists
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: register.php?error=Username or email already exists");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    $stmt->execute();
    
    if ($stmt->affected_rows === 1) {
        header("Location: index.php?success=Registration successful. Please login.");
        exit();
    } else {
        header("Location: register.php?error=Registration failed. Please try again.");
        exit();
    }
}

// Logout process
if (isset($_GET['logout'])) {
    // Destroy session
    session_destroy();
    header("Location: index.php");
    exit();
}