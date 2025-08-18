<?php
session_start();
include("../connection.php");

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header('HTTP/1.1 403 Forbidden');
    die(json_encode(['error' => 'Session expired. Please login again.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'], $_POST['status'])) {
    $job_id = intval($_POST['job_id']);
    $status = $_POST['status'];
    $stationary_id = $_SESSION['stationary_admin_id'];

    // Validate status
    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        header('HTTP/1.1 400 Bad Request');
        die(json_encode(['error' => 'Invalid status']));
    }

    // Verify the job belongs to this stationary
    $check_query = "SELECT job_id FROM print_jobs WHERE job_id = ? AND stationery_id = ?";
    $check_stmt = $conn->prepare($check_query);
    
    if (!$check_stmt) {
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(['error' => 'Database error']));
    }

    $check_stmt->bind_param("ii", $job_id, $stationary_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        header('HTTP/1.1 403 Forbidden');
        die(json_encode(['error' => 'Job not found or not authorized']));
    }

    // Update the status
    $update_query = "UPDATE print_jobs SET status = ? WHERE job_id = ?";
    $update_stmt = $conn->prepare($update_query);
    
    if (!$update_stmt) {
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(['error' => 'Database error']));
    }

    $update_stmt->bind_param("si", $status, $job_id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => 'Status updated successfully']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Error updating status']);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Invalid request']));
}