<?php
require ('dbconn.php');

if (isset($_GET['membership_no']) && isset($_GET['book_id']) && isset($_GET['dues'])) {
    $membershipNo = $_GET['membership_no'];
    $bookId = $_GET['book_id'];
    $dues = $_GET['dues'];

    // Generate bill
    echo "<h2>Bill Generation</h2>";
    echo "<p>Membership Number: $membershipNo</p>";
    echo "<p>Book ID: $bookId</p>";
    echo "<p>Dues: LKR $dues</p>";

    // Update the bill status in the database
    $updateQuery = "UPDATE record 
                    SET Bill_Status = 'Paid' 
                    WHERE MembershipNo = ? AND BookId = ? AND Date_of_Return IS NULL";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $membershipNo, $bookId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p>Bill generated successfully.</p>";
    } else {
        echo "<p>Error generating bill: " . $conn->error . "</p>";
    }

    // Update renewal and return date/time if needed
    $updateRenewalQuery = "UPDATE record 
                           SET Renewals = Renewals + 1,
                               Due_Date = DATE_ADD(Due_Date, INTERVAL 14 DAY),
                               Date_of_Return = CURRENT_TIMESTAMP 
                           WHERE MembershipNo = ? AND BookId = ?";
    $stmtRenewal = $conn->prepare($updateRenewalQuery);
    $stmtRenewal->bind_param("si", $membershipNo, $bookId);
    $stmtRenewal->execute();

    if ($stmtRenewal->affected_rows > 0) {
        echo "<p>Renewed successfully.</p>";
    } else {
        echo "<p>Error renewing book: " . $conn->error . "</p>";
    }
}

$conn->close();
?>