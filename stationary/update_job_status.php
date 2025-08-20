<?php
session_start();
include("../connection.php");

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header("Location: stationary_login.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'], $_POST['status'])) {
    $job_id = intval($_POST['job_id']);
    $status = $_POST['status'];
    $stationary_id = $_SESSION['stationary_admin_id'];

    // Validate status
    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        header("Location: print_requests.php?error=invalid_status");
        exit();
    }

    // Verify the job belongs to this stationary
    $check_query = "SELECT job_id FROM print_jobs WHERE job_id = ? AND stationery_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $job_id, $stationary_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        header("Location: print_requests.php?error=not_authorized");
        exit();
    }

    // Update the status
    $update_query = "UPDATE print_jobs SET status = ? WHERE job_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $status, $job_id);

    if ($update_stmt->execute()) {
        header("Location: print_requests.php?success=1");
        exit();
    } else {
        header("Location: print_requests.php?error=update_failed");
        exit();
    }
} else {
    header("Location: print_requests.php?error=invalid_request");
    exit();
}
