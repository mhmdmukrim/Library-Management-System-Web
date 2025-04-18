<?php
require('dbconn.php');
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_POST['membership_no'], $_POST['book_id'], $_POST['record_id'])) {
        $membershipNo = $_POST['membership_no'];
        $bookId = $_POST['book_id'];
        $recordId = $_POST['record_id'];

        // Fetch current dues and due date using prepared statement
        $fetchDataQuery = "SELECT MembershipNo, BookId, Dues, PrDues, Due_Date, DATEDIFF(CURDATE(), Due_Date) AS overdue_days FROM record WHERE record_id = ?";
        $stmt = $conn->prepare($fetchDataQuery);
        $stmt->bind_param("i", $recordId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentDues = $row['Dues'];
            $previousDues = $row['PrDues'];
            $currentDueDate = $row['Due_Date'];
            $overdueDays = $row['overdue_days'];

            // Calculate new Due_Date based on current date + 14 days
            $newDueDate = date('Y-m-d', strtotime('+14 days'));
            $today = date('Y-m-d');

            // Transfer current dues to PrDues
            $newPreviousDues = $previousDues + $currentDues;

            if ($currentDueDate < $today) { // Check if current due date has passed
                // Update the Due_Date, Renewals, Dues, and PrDues using a prepared statement
                $updateQuery = "UPDATE record 
                                SET Due_Date = ?, 
                                    Renewals = Renewals + 1, 
                                    Dues = 0, 
                                    PrDues = ? 
                                WHERE record_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sii", $newDueDate, $newPreviousDues, $recordId);

                if ($stmt->execute()) {
                    // Fetch user's email address
                    $fetchEmailQuery = "SELECT EmailId FROM user WHERE MembershipNo = ?";
                    $stmt = $conn->prepare($fetchEmailQuery);
                    $stmt->bind_param("i", $membershipNo);
                    $stmt->execute();
                    $emailResult = $stmt->get_result();

                    if ($emailResult->num_rows > 0) {
                        $emailRow = $emailResult->fetch_assoc();
                        $userEmail = $emailRow['EmailId'];

                        // Prepare email details using PHPMailer
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
                            $mail->Subject = 'Book Renewal Notification';
                            $mail->Body    = "
                                <h2>Bill Generation</h2>
                                <p>Membership Number: $membershipNo</p>
                                <p>Book ID: $bookId</p>
                                <p>Dues: $newPreviousDues LKR</p>
                                <p>Next Due Date: $newDueDate</p>
                                <p>Bill Status: Unpaid</p>
                            ";

                            $mail->send();
                            echo "<script>console.log('Email sent successfully');</script>";
                        } catch (Exception $e) {
                            echo "<script>console.log('Email sending failed: {$mail->ErrorInfo}');</script>";
                        }

                        // Generate bill and show in a popup window
                        echo "<script>
                                var billDetails = `
                                    <h2>Bill Generation</h2>
                                    <p>Membership Number: $membershipNo</p>
                                    <p>Book ID: $bookId</p>
                                    <p>Dues: $newPreviousDues LKR</p>
                                    <p>Next Due Date: $newDueDate</p>
                                    <p>Bill Status: Unpaid</p>
                                `;
                                var billWindow = window.open('', '_blank', 'width=600,height=400');
                                billWindow.document.body.innerHTML = billDetails;
                                billWindow.print();
                                setTimeout(function() {
                                    window.location.href = 'renew_requests.php';
                                }, 1000); // Redirect after 1 second (adjust as needed)
                            </script>";
                    } else {
                        echo "<script>alert('Error: Email not found.');
                                setTimeout(function() {
                                    window.location.href = 'renew_requests.php';
                                }, 100); // Redirect after 0.1 second
                            </script>";
                    }
                } else {
                    echo "<script>alert('Error updating record: " . $stmt->error . "');
                    setTimeout(function() {
                            window.location.href = 'renew_requests.php';
                        }, 100); // Redirect after 0.1 second
                        </script>";
                }
            } else {
                echo "<script>
                        alert('Due Date is still available.');
                        setTimeout(function() {
                            window.location.href = 'renew_requests.php';
                        }, 100); // Redirect after 0.1 second
                    </script>";
            }
        } else {
            echo "<script>alert('Error: Record not found.');
                        setTimeout(function() {
                            window.location.href = 'renew_requests.php';
                        }, 100); // Redirect after 0.1 second
                        </script>";
        }
    } else {
        echo "<script>alert('Invalid request: Missing parameters.');
        setTimeout(function() {
                            window.location.href = 'renew_requests.php';
                        }, 100); // Redirect after 0.1 second
                        </script>";
    }
} else {
    echo "<script>alert('Invalid request method.');</script>";
}
?>
