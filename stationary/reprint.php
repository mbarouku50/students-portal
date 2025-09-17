<?php
session_name('admin_session');
session_start();

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin_login.php');
    exit();
}

$stationery_id = $_SESSION['stationary_id'] ?? null;
if (!$stationery_id) {
    die("Stationery ID not found in session.");
}

if (isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
    
    // Verify job exists and belongs to this station
    $query = "SELECT * FROM print_jobs WHERE job_id = ? AND stationery_id = ? AND status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $job_id, $stationery_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
        
        // Create a new print job based on the old one
        $insert_query = "INSERT INTO print_jobs (
            stationery_id, 
            user_name, 
            phone_number, 
            content, 
            file_path, 
            print_type, 
            copies, 
            special_instructions,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param(
            "isssssis",
            $stationery_id,
            $job['user_name'],
            $job['phone_number'],
            $job['content'],
            $job['file_path'],
            $job['print_type'],
            $job['copies'],
            $job['special_instructions']
        );
        
        if ($stmt->execute()) {
            header("Location: print_requests.php?reprint_success=1");
        } else {
            header("Location: print_requests.php?reprint_error=1");
        }
        exit();
    } else {
        die("Invalid job or unauthorized access.");
    }
} else {
    header("Location: print_requests.php");
    exit();
}
?>