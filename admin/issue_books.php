<?php
require('dbconn.php');
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendIssueEmail($email, $membershipNo, $bookTitle, $issueDate, $dueDate) {
    $mail = new PHPMailer();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mukrimmhmd@gmail.com';
        $mail->Password = 'iguk mpfl rjot boxw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('mukrimmhmd@gmail.com', 'Akurana Public Library');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Book Issued Successfully';
        $mail->Body = "Dear Member,<br><br>The book <b>$bookTitle</b> (Membership No: $membershipNo) has been issued to you on <b>$issueDate</b>.<br><br>The due date for returning the book is <b>$dueDate</b>.<br><br>Best regards,<br>Akurana Public Library";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SESSION['MembershipNo']) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $membershipNo = $_POST['membershipNo'];
        $bookId = $_POST['bookId'];

        // Check if the user exists
        $sql_user = "SELECT * FROM LMS.user WHERE MembershipNo = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $membershipNo);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows == 0) {
            echo "<script type='text/javascript'>alert('Invalid Membership Number!'); window.location.href='issue_requests.php';</script>";
            exit();
        }

        $user = $result_user->fetch_assoc();
        $email = $user['EmailId'];

        // Check if the book exists and its availability
        $sql_book = "SELECT * FROM LMS.book WHERE BookId = ?";
        $stmt_book = $conn->prepare($sql_book);
        $stmt_book->bind_param("i", $bookId);
        $stmt_book->execute();
        $result_book = $stmt_book->get_result();

        if ($result_book->num_rows == 0) {
            echo "<script type='text/javascript'>alert('Invalid Book ID!'); window.location.href='issue_requests.php';</script>";
            exit();
        }

        $book = $result_book->fetch_assoc();
        $bookTitle = $book['Title'];
        if ($book['Availability'] <= 0) {
            echo "<script type='text/javascript'>alert('Book not available!'); window.location.href='issue_requests.php';</script>";
            exit();
        }

        // Check if there's already a pending request or issued book for the same user and book
        $sql_record = "SELECT * FROM LMS.record WHERE MembershipNo = ? AND BookId = ? AND Date_of_Return IS NULL";
        $stmt_record = $conn->prepare($sql_record);
        $stmt_record->bind_param("si", $membershipNo, $bookId);
        $stmt_record->execute();
        $result_record = $stmt_record->get_result();

        if ($result_record->num_rows > 0) {
            echo "<script type='text/javascript'>alert('Book already issued to this member.'); window.location.href='issue_requests.php';</script>";
            exit();
        }

        // Calculate due date (14 days from today)
        $issueDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime($issueDate . ' + 14 days'));

        // Insert the record and update the book's availability
        $conn->begin_transaction();
        try {
            $sql_insert = "INSERT INTO LMS.record (MembershipNo, BookId, Date_of_Issue, Due_Date) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("siss", $membershipNo, $bookId, $issueDate, $dueDate);
            $stmt_insert->execute();

            $sql_update = "UPDATE LMS.book SET Availability = Availability - 1 WHERE BookId = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $bookId);
            $stmt_update->execute();

            $conn->commit();

            // Send email notification
            sendIssueEmail($email, $membershipNo, $bookTitle, $issueDate, $dueDate);

            echo "<script type='text/javascript'>alert('Book issued successfully.'); window.location.href='issue_requests.php';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script type='text/javascript'>alert('Failed to issue book. Please try again later.'); window.location.href='issue_requests.php';</script>";
        }
    }
} else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
}
?>
