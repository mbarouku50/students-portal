<?php
include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $stationery_id = intval($_POST['station']);
    $copies = intval($_POST['copies']);
    $color = $_POST['color'] == 'color' ? 'color' : 'black';
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // File upload handling
    $target_dir = "uploads/";
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . uniqid() . '_' . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check file type
    $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
    if (!in_array($file_type, $allowed_types)) {
        die("Sorry, only PDF, DOC, DOCX, PPT, PPTX files are allowed.");
    }
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO print_jobs (user_name, phone_number, stationery_id, file_path, copies, print_type, special_instructions)
                VALUES ('$name', '$phone', $stationery_id, '$target_file', $copies, '$color', '$notes')";
        
        if ($conn->query($sql)) {
            echo "<script>alert('File uploaded and print job submitted successfully!'); window.location.href='print_option.php';</script>";
        } else {
            unlink($target_file); // Delete the uploaded file if DB insert fails
            echo "<script>alert('Error submitting print job: " . $conn->error . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.'); window.history.back();</script>";
    }
} else {
    header("Location: print_option.php");
}
?>