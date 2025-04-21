<?php
// task_handler.php - Handles CRUD operations for tasks

session_start();
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add new task
if (isset($_POST['add_task'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    
    // Validate inputs
    if (empty($judul) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        header("Location: dashboard.php?error=Required fields cannot be empty");
        exit();
    }
    
    // Insert task
    $sql = "INSERT INTO tasks (user_id, judul, deskripsi, status, tanggal_mulai, tanggal_selesai) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $judul, $deskripsi, $status, $tanggal_mulai, $tanggal_selesai);
    $stmt->execute();
    
    if ($stmt->affected_rows === 1) {
        header("Location: dashboard.php?success=Task added successfully");
    } else {
        header("Location: dashboard.php?error=Failed to add task");
    }
    exit();
}

// Update task
if (isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    
    // Validate inputs
    if (empty($judul) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        header("Location: dashboard.php?error=Required fields cannot be empty");
        exit();
    }
    
    // Verify task belongs to current user
    $sql = "SELECT user_id FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $task = $result->fetch_assoc();
        if ($task['user_id'] !== $user_id) {
            header("Location: dashboard.php?error=Unauthorized access");
            exit();
        }
    } else {
        header("Location: dashboard.php?error=Task not found");
        exit();
    }
    
    // Update task
    $sql = "UPDATE tasks SET judul = ?, deskripsi = ?, status = ?, tanggal_mulai = ?, tanggal_selesai = ? 
            WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $judul, $deskripsi, $status, $tanggal_mulai, $tanggal_selesai, $task_id, $user_id);
    $stmt->execute();
    
    if ($stmt->affected_rows >= 0) {
        header("Location: dashboard.php?success=Task updated successfully");
    } else {
        header("Location: dashboard.php?error=Failed to update task");
    }
    exit();
}

// Delete task
if (isset($_POST['delete_task'])) {
    $task_id = $_POST['task_id'];
    
    // Verify task belongs to current user
    $sql = "SELECT user_id FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $task = $result->fetch_assoc();
        if ($task['user_id'] !== $user_id) {
            header("Location: dashboard.php?error=Unauthorized access");
            exit();
        }
    } else {
        header("Location: dashboard.php?error=Task not found");
        exit();
    }
    
    // Delete task
    $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    
    if ($stmt->affected_rows === 1) {
        header("Location: dashboard.php?success=Task deleted successfully");
    } else {
        header("Location: dashboard.php?error=Failed to delete task");
    }
    exit();
}