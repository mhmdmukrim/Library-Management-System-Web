<?php
require ('dbconn.php');

$query = "SELECT b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price, GROUP_CONCAT(a.Author SEPARATOR ', ') as Authors
          FROM book b
          LEFT JOIN author a ON b.BookId = a.BookId
          GROUP BY b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Books Report</title>
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
    <?php require 'header.php'; ?>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php require 'sidebar.php'; ?>
                <div class="span9 printableArea">
                    <h2>All Books Report</h2>
                    <?php if ($result->num_rows > 0) { ?>
                        <table class="pr-table">
                            <thead>
                                <tr>
                                    <th>Book ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>ISBN</th>
                                    <th>Publisher</th>
                                    <th>Year</th>
                                    <th>Price (LKR)</th>
                                    <th>Authors</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['BookId']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Category']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ISBN']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Publisher']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Year']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Authors']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <p>No records found.</p>
                    <?php } ?>

                    <div class="button-container">
                        <button onclick="window.location.href='report.php'" class="btn btn-primary">Back to Report
                            Page</button>
                        <button onclick="printReport()" class="btn btn-secondary">Print</button>
                        <form method="POST" action="download_all_books.php" target="_blank">
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

    <?php require 'footer.php'; ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="scripts/common.js" type="text/javascript"></script>
</body>

</html>