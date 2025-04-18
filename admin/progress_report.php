<?php
require ('dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    $membershipNo = isset($_POST['membership_no']) ? $_POST['membership_no'] : '';

    // Prepare and execute the query
    $query = "SELECT r.record_id, r.MembershipNo, m.Name, r.BookId, b.Title, r.Date_of_Issue, r.Due_Date, r.Date_of_Return, r.trfDues, r.Renewals, r.Bill_Status
              FROM record r
              JOIN user m ON r.MembershipNo = m.MembershipNo
              JOIN book b ON r.BookId = b.BookId
              WHERE r.Date_of_Issue BETWEEN ? AND ?";
    if (!empty($membershipNo)) {
        $query .= " AND r.MembershipNo = ?";
    }

    $stmt = $conn->prepare($query);
    if (!empty($membershipNo)) {
        $stmt->bind_param("sss", $fromDate, $toDate, $membershipNo);
    } else {
        $stmt->bind_param("ss", $fromDate, $toDate);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Calculate total transactions and total amount
    $totalAmount = 0;
    $totalTransactions = $result->num_rows;
    while ($row = $result->fetch_assoc()) {
        $totalAmount += $row['trfDues'];
    }

    $result->data_seek(0); // Reset pointer to the beginning of the result set
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Progress Report</title>
        <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/theme.css">
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 8px 12px;
                border: 1px solid #ddd;
            }

            th {
                background-color: #f2f2f2;
            }

            .button-container {
                margin: 20px 0;
                text-align: center;
            }

            @media print {
                body * {
                    visibility: hidden;
                }

                .printableArea,
                .printableArea * {
                    visibility: visible;
                }

                .printableArea {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                .button-container {
                    display: none;
                }
            }
        </style>
        <script>
            function printReport() {
                window.print();
            }
        </script>
    </head>

    <body>
        <?php
        require 'header.php';
        ?>
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <?php
                    require 'sidebar.php';
                    ?>
                    <div class="span9 printableArea">
                        <h2>Progress Report from <?php echo htmlspecialchars($fromDate); ?> to
                            <?php echo htmlspecialchars($toDate); ?>
                        </h2>

                        <?php if ($totalTransactions > 0) { ?>
                            <table class="pr-table">
                                <thead>
                                    <tr>
                                        <th>Record ID</th>
                                        <th>Membership No</th>
                                        <th>Name</th>
                                        <th>Book ID</th>
                                        <th>Title</th>
                                        <th>Date of Issue</th>
                                        <th>Due Date</th>
                                        <th>Date of Return</th>
                                        <th>Paid Dues</th>
                                        <th>Renewals</th>
                                        <th>Bill Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['MembershipNo']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['BookId']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Date_of_Issue']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Due_Date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Date_of_Return']); ?></td>
                                            <td><?php echo htmlspecialchars($row['trfDues']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Renewals']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Bill_Status']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div>
                                <h4>Total Transactions: <?php echo $totalTransactions; ?></h4>
                                <h4>Total Amount: LKR <?php echo number_format($totalAmount, 2); ?></h4>
                            </div>
                        <?php } else { ?>
                            <p>No records found for the given date range.</p>
                        <?php } ?>

                        <div class="button-container">
                            <button onclick="window.location.href='report.php'" class="btn btn-primary">Back to Report
                                Page</button>
                            <button onclick="printReport()" class="btn btn-secondary">Print</button>
                            <form method="POST" action="download_report.php" target="_blank">
                                <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
                                <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
                                <input type="hidden" name="membership_no"
                                    value="<?php echo htmlspecialchars($membershipNo); ?>">
                                <select name="file_format" required>
                                    <option value="" disabled selected>Save as...</option>
                                    <option value="pdf">PDF</option>
                                    <option value="doc">DOC</option>
                                    <option value="xlsx">XLSX</option>
                                    <option value="sql">SQL Data</option>
                                </select>
                                <input type="submit" value="Download" class="btn btn-info">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        require 'footer.php';
        ?>


        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
        <script src="scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="scripts/common.js" type="text/javascript"></script>
    </body>

    </html>
    <?php
} else {
    echo "<script type='text/javascript'>alert('Invalid request method.');</script>";
}
?>