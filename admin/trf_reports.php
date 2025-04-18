<?php
require ('dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    $membershipNo = isset($_POST['membership_no']) ? $_POST['membership_no'] : '';

    // Prepare and execute the query
    $query = "SELECT r.trf_date, SUM(r.trfDues) as total_trfDues
              FROM record r
              WHERE r.trf_date BETWEEN ? AND ?";
    if (!empty($membershipNo)) {
        $query .= " AND r.MembershipNo = ?";
    }
    $query .= " GROUP BY r.trf_date";

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
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Transfer Report</title>
        <link rel="icon" href="images/rbg.png" type="image/icon type">
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
                        <h2>Transfer Report from <?php echo htmlspecialchars($fromDate); ?> to
                            <?php echo htmlspecialchars($toDate); ?>
                        </h2>

                        <?php if ($totalTransactions > 0) { ?>
                            <table class="pr-table">
                                <thead>
                                    <tr>
                                        <th>Transfer Date</th>
                                        <th>Transferred Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['trf_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['total_trfDues']); ?></td>
                                        </tr>
                                        <?php
                                        $totalAmount += $row['total_trfDues'];
                                    } ?>
                                </tbody>
                            </table>
                            <h3>Total Amount Transferred: <?php echo $totalAmount; ?></h3>
                        <?php } else { ?>
                            <p>No records found for the given date range.</p>
                        <?php } ?>

                        <div class="button-container">
                            <button onclick="window.location.href='report.php'" class="btn btn-primary">Back to Report
                                Page</button>
                            <button onclick="printReport()" class="btn btn-secondary">Print</button>
                            <form method="POST" action="download_trf.php" target="_blank">
                                <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
                                <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
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