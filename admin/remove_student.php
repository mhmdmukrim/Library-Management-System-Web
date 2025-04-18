<?php
require('dbconn.php'); // Include your database connection file

// Check if the session variable for the user is set
if (!isset($_SESSION['MembershipNo'])) {
    echo "<script>alert('Access Denied! You are not logged in.'); window.location='index.php';</script>";
    exit();
}

// Check if the student ID is passed via GET request
if (isset($_GET['id'])) {
    $MembershipNo = $_GET['id'];

    // Disable foreign key checks temporarily
    $conn->query("SET foreign_key_checks = 0");

    // Check if there are associated records in LMS.record
    $checkIssuedBooks = $conn->prepare("SELECT * FROM LMS.record WHERE MembershipNo = ?");
    if (!$checkIssuedBooks) {
        error_log("Error preparing query for issued books: " . $conn->error);
        echo "<script>alert('Database error.'); window.location='student.php';</script>";
        exit();
    }
    $checkIssuedBooks->bind_param("s", $MembershipNo);
    $checkIssuedBooks->execute();
    $issuedBooks = $checkIssuedBooks->get_result();

    // If there are associated records, delete them first
    if ($issuedBooks->num_rows > 0) {
        $deleteRecordsQuery = $conn->prepare("DELETE FROM LMS.record WHERE MembershipNo = ?");
        if (!$deleteRecordsQuery) {
            error_log("Error preparing delete records query: " . $conn->error);
            echo "<script>alert('Database error.'); window.location='student.php';</script>";
            exit();
        }
        $deleteRecordsQuery->bind_param("s", $MembershipNo);
        $deleteRecordsQuery->execute();

        // Check if deletion of records was successful
        if ($deleteRecordsQuery->affected_rows > 0) {
            echo "<script>alert('Associated records deleted successfully.'); window.location='remove_student.php?id=$MembershipNo';</script>";
            // After deleting associated records, continue with deleting the user below
        } else {
            error_log("Failed to delete associated records. Affected rows: " . $deleteRecordsQuery->affected_rows);
            echo "<script>alert('Failed to delete associated records.'); window.location='student.php';</script>";
            exit();
        }
    }

    // Proceed with deleting the student record
    $deleteQuery = $conn->prepare("DELETE FROM LMS.user WHERE MembershipNo = ?");
    if (!$deleteQuery) {
        error_log("Error preparing delete user query: " . $conn->error);
        echo "<script>alert('Database error.'); window.location='student.php';</script>";
        exit();
    }
    $deleteQuery->bind_param("s", $MembershipNo);
    $deleteQuery->execute();

    // Check if the deletion was successful
    if ($deleteQuery->affected_rows > 0) {
        echo "<script>alert('Member removed successfully!'); window.location='student.php';</script>";
    } else {
        error_log("Failed to remove Member. Affected rows: " . $deleteQuery->affected_rows);
        echo "<script>alert('Failed to remove Member.'); window.location='student.php';</script>";
    }

    // Re-enable foreign key checks
    $conn->query("SET foreign_key_checks = 1");
} else {
    echo "<script>alert('Invalid Request.'); window.location='student.php';</script>";
}
?>
