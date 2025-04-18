<?php
session_start();

require('dbconn.php'); // Ensure this is correctly included and configured

// Check if user is logged in
if (!isset($_SESSION['MembershipNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied! You are not logged in.'); window.location='index.php';</script>";
    exit();
}

// Check if the book ID is provided
if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    // Start transaction for consistency
    $conn->begin_transaction();

    try {
        // Delete related records in the 'author' table
        $deleteAuthorsStmt = $conn->prepare("DELETE FROM author WHERE BookId = ?");
        $deleteAuthorsStmt->bind_param("i", $bookId);
        $deleteAuthorsStmt->execute();

        // Delete the book from the 'book' table
        $deleteBookStmt = $conn->prepare("DELETE FROM book WHERE BookId = ?");
        $deleteBookStmt->bind_param("i", $bookId);

        // Check if the book deletion is successful
        if ($deleteBookStmt->execute()) {
            // Commit the transaction
            $conn->commit();
            echo "<script type='text/javascript'>alert('Book successfully deleted');</script>";
            echo "<script type='text/javascript'>document.location.href = 'book.php';</script>";
        } else {
            // Rollback the transaction if book deletion fails
            $conn->rollback();
            echo "<script type='text/javascript'>alert('Failed to remove book');</script>";
        }
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "<script type='text/javascript'>alert('An error occurred: " . $e->getMessage() . "');</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Invalid Book ID');</script>";
}
?>
