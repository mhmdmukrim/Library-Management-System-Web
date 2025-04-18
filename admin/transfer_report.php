<!DOCTYPE html>
<html>

<head>
    <title>Total Amount Transferred Report</title>
    <link rel="icon" href="images/rbg.png" type="image/icon type">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
        rel='stylesheet'>
    <style>
        /* Add some custom styles */
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .no-print {
            display: inline-block;
            margin: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        require ('dbconn.php');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Fetch records where PaidDues is not 0 or null
            $query = "SELECT record_id, MembershipNo, BookId, Date_of_Issue, Due_Date, Date_of_Return, PaidDues
                      FROM record
                      WHERE PaidDues IS NOT NULL AND PaidDues <> 0";
            $result = $conn->query($query);

            $reportHTML = "";
            $totalAmount = 0;
            $currentDate = date('Y-m-d');

            if ($result->num_rows > 0) {
                $reportHTML .= "<h1>Akurana Public Library</h1>";
                $reportHTML .= "<h2>Total Amount Transferred to Hand Report</h2>";
                $reportHTML .= "<table class='table table-bordered table-striped'>
                        <tr>
                            <th>Record ID</th>
                            <th>Membership No</th>
                            <th>Book ID</th>
                            <th>Date of Issue</th>
                            <th>Due Date</th>
                            <th>Date of Return</th>
                            <th>Paid Dues</th>
                        </tr>";

                while ($row = $result->fetch_assoc()) {
                    $reportHTML .= "<tr>
                            <td>{$row['record_id']}</td>
                            <td>{$row['MembershipNo']}</td>
                            <td>{$row['BookId']}</td>
                            <td>{$row['Date_of_Issue']}</td>
                            <td>{$row['Due_Date']}</td>
                            <td>{$row['Date_of_Return']}</td>
                            <td>{$row['PaidDues']}</td>
                        </tr>";

                    $totalAmount += $row['PaidDues'];

                    // Update the PaidDues to 0, set trfDues, and update trf_date
                    $updateQuery = "UPDATE record
                                    SET PaidDues = 0, trfDues = ?, trf_date = ?
                                    WHERE record_id = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("isi", $row['PaidDues'], $currentDate, $row['record_id']);
                    $stmt->execute();
                }

                $reportHTML .= "</table>";
                $reportHTML .= "<h3>Total Amount Transferred: $totalAmount</h3>";
            } else {
                $reportHTML .= "<h1>Akurana Public Library</h1>";
                $reportHTML .= "No records found.";
            }

            echo $reportHTML;
        } else {
            echo "Invalid request method.";
        }
        ?>

        <button class="btn btn-primary no-print" type="button" onclick="window.print();">Print</button>
    </div>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="scripts/common.js" type="text/javascript"></script>
</body>

</html>
