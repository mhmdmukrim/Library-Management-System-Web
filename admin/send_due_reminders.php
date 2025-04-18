<?php
require('dbconn.php');
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendReminderEmail($email, $membershipNo, $bookTitle, $dueDate, $reminder, $daysDue = 0, $dueAmount = 0)
{
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
        if ($daysDue > 0) {
            $mail->Subject = "Book Overdue Notice - $daysDue days late";
            $mail->Body = "Dear Member,<br><br>Your book <b>$bookTitle</b> (Membership No: $membershipNo) was due on <b>$dueDate</b> and is now <b>$daysDue days overdue</b>. The total due amount is <b>LKR $dueAmount</b>.<br><br>Please return or renew the book as soon as possible to avoid further charges.<br><br>Best regards,<br>Akurana Public Library";
        } else {
            $mail->Subject = "Book Due Date Reminder $reminder";
            $mail->Body = "Dear Member,<br><br>This is Reminder #$reminder that the book <b>$bookTitle</b> (Membership No: $membershipNo) is due on <b>$dueDate</b>.<br><br>Please return or renew the book by the due date to avoid any late fees.<br><br>Best regards,<br>Akurana Public Library";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$today = date('Y-m-d');
$sql = "SELECT user.EmailId, user.MembershipNo, book.Title, record.Due_Date, record.record_id, record.reminder
        FROM LMS.record 
        JOIN LMS.user ON record.MembershipNo = user.MembershipNo 
        JOIN LMS.book ON record.BookId = book.Bookid 
        WHERE record.Date_of_Return IS NULL";

$result = $conn->query($sql);

$emailDetails = "<table style='width:100%; border-collapse: collapse;'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Membership No</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Email</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Book Title</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Due Date</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Reminder Count</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Days Overdue</th>
                            <th style='border: 1px solid #ddd; padding: 8px;'>Due Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>";

$hasReminders = false;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['EmailId'];
        $membershipNo = $row['MembershipNo'];
        $bookTitle = $row['Title'];
        $dueDate = $row['Due_Date'];
        $recordID = $row['record_id'];
        $reminder = intval($row['reminder']) + 1;

        $dueDays = (strtotime($today) - strtotime($dueDate)) / (60 * 60 * 24);
        $dueAmount = $dueDays > 0 ? $dueDays * 1 : 0; // Assume LKR 10 per day overdue fee

        if (sendReminderEmail($email, $membershipNo, $bookTitle, $dueDate, $reminder, max(0, $dueDays), $dueAmount)) {
            $emailDetails .= "<tr>
                                <td style='border: 1px solid #ddd; padding: 8px;'>$membershipNo</td>
                                <td style='border: 1px solid #ddd; padding: 8px;'>$email</td>
                                <td style='border: 1px solid #ddd; padding: 8px;'>$bookTitle</td>
                                <td style='border: 1px solid #ddd; padding: 8px;'>$dueDate</td>
                                <td style='border: 1px solid #ddd; padding: 8px;'>$reminder</td>
                                <td style='border: 1px solid #ddd; padding: 8px;'>$dueDays</td>
                                <td style='border: 1px solid #ddd; padding: 8px;'>LKR $dueAmount</td>
                              </tr>";

            $updateSql = "UPDATE LMS.record SET reminder = $reminder WHERE record_id = $recordID";
            $conn->query($updateSql);
            $hasReminders = true;
        }
    }
}

$emailDetails .= "</tbody></table>";

if (!$hasReminders) {
    echo "<script>alert('No reminders to send.'); window.location.href = 'index.php';</script>";
    exit;
}

// Redirect after sending emails
header("Location: index.php");
exit;
