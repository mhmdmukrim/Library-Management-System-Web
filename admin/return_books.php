<?php
require('dbconn.php');
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fee rate per day
$feeRatePerDay = 1; // LKR 1 per day overdue

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['membership_no'], $_POST['book_id'], $_POST['record_id'])) {
        $membershipNo = $_POST['membership_no'];
        $bookId = $_POST['book_id'];
        $recordId = $_POST['record_id'];

        // Fetch current dues and due date using prepared statement
        $fetchDataQuery = "SELECT Dues, Due_Date, PrDues, PaidDues, DATEDIFF(CURDATE(), Due_Date) AS overdueDays FROM record WHERE MembershipNo = ? AND BookId = ? AND record_id = ?";
        $stmt = $conn->prepare($fetchDataQuery);
        $stmt->bind_param("sii", $membershipNo, $bookId, $recordId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentDues = $row['Dues'];
            $currentDueDate = $row['Due_Date'];
            $previousDues = $row['PrDues'];
            $paidDues = $row['PaidDues'];
            $overdueDays = $row['overdueDays'];

            // Calculate overdue amount
            $overdueAmount = max(0, $overdueDays * $feeRatePerDay);
            $totalDues = max(0, $previousDues + $overdueAmount - $paidDues);

            // Generate bill and show in a popup window
            echo "<script>
                    var billDetails = `
                        <h2>Bill Generation</h2>
                        <p>Membership Number: $membershipNo</p>
                        <p>Book ID: $bookId</p>
                        <p>Dues: $totalDues LKR</p>
                        <p>Bill Status: Paid</p>
                    `;
                    var billWindow = window.open('', '_blank', 'width=600,height=400');
                    billWindow.document.body.innerHTML = billDetails;
                    billWindow.print();
                </script>";

            // Update the return status, clear dues, set return date, make the book available, and update trfDues using prepared statement
            $conn->begin_transaction();
            try {
                $updateQuery = "UPDATE record
                                SET Dues = 0,
                                    Bill_Status = 'Paid',
                                    Date_of_Return = CURDATE(),  -- Set Date_of_Return to current date
                                    PaidDues = ?,
                                    PrDues = 0
                                WHERE MembershipNo = ? AND BookId = ? AND record_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("dssi", $totalDues, $membershipNo, $bookId, $recordId);

                if (!$stmt->execute()) {
                    throw new Exception("Error updating record: " . $stmt->error);
                }

                // Update the book's availability
                $updateBookQuery = "UPDATE LMS.book 
                                    SET Availability = Availability + 1 
                                    WHERE BookId = ?";
                $stmt = $conn->prepare($updateBookQuery);
                $stmt->bind_param("i", $bookId);

                if (!$stmt->execute()) {
                    throw new Exception("Error updating book availability: " . $stmt->error);
                }

                // Fetch user's email address
                $fetchEmailQuery = "SELECT EmailId FROM user WHERE MembershipNo = ?";
                $stmt = $conn->prepare($fetchEmailQuery);
                $stmt->bind_param("s", $membershipNo);
                $stmt->execute();
                $emailResult = $stmt->get_result();

                if ($emailResult->num_rows > 0) {
                    $emailRow = $emailResult->fetch_assoc();
                    $userEmail = $emailRow['EmailId'];

                    // Send email notification using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                        $mail->SMTPAuth = true;
                        $mail->Username = 'mukrimmhmd@gmail.com'; // SMTP username
                        $mail->Password = 'iguk mpfl rjot boxw'; // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipients
                        $mail->setFrom('mukrimmhmd@gmail.com', 'Library Admin');
                        $mail->addAddress($userEmail);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Library Book Return Confirmation';
                        $mail->Body    = "
                            <html>
                            <head>
                                <title>Book Return Confirmation</title>
                            </head>
                            <body>
                                <h2>Book Return Confirmation</h2>
                                <p>Dear Member,</p>
                                <p>Your book return has been processed successfully.</p>
                                <p>Details:</p>
                                <p>Membership Number: $membershipNo</p>
                                <p>Book ID: $bookId</p>
                                <p>Total Dues Paid: LKR $totalDues</p>
                                <p>Thank you for using the Akurana Public Library.</p>
                                <p>Best regards,</p>
                                <p>Akurana Public Library</p>
                            </body>
                            </html>
                        ";

                        $mail->send();
                        echo "<script>console.log('Email sent successfully');</script>";
                    } catch (Exception $e) {
                        echo "<script>console.log('Email sending failed: {$mail->ErrorInfo}');</script>";
                    }
                }

                $conn->commit();

                // Redirect to return_requests.php after 1 second
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'return_requests.php';
                        }, 1000); // Redirect after 1 second (adjust as needed)
                    </script>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
            }
        } else {
            echo "<script>alert('Error: Book not found for the specified user.');</script>";
        }
    } else {
        // Debugging: Check what is missing
        $missing = [];
        if (!isset($_POST['membership_no'])) {
            $missing[] = 'membership_no';
        }
        if (!isset($_POST['book_id'])) {
            $missing[] = 'book_id';
        }
        if (!isset($_POST['record_id'])) {
            $missing[] = 'record_id';
        }
        echo "<script>alert('Invalid request: Missing parameters: " . implode(', ', $missing) . "');</script>";
    }
} else {
    echo "<script>alert('Invalid request method.');</script>";
}
?>
