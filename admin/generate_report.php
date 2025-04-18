<?php
require ('dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromBookId = $_POST['from_book_id'];
    $toBookId = $_POST['to_book_id'];

    // Prepare and execute the query
    $query = "SELECT b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price, GROUP_CONCAT(a.Author SEPARATOR ', ') as Authors
    FROM book b
    LEFT JOIN author a ON b.BookId = a.BookId
    WHERE b.BookId BETWEEN ? AND ?
    GROUP BY b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price";



    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $fromBookId, $toBookId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Calculate total transactions
    $totalBooks = $result->num_rows;

    $result->data_seek(0); // Reset pointer to the beginning of the result set
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Report Preview</title>
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
                        <h2>Book Report from Book ID <?php echo htmlspecialchars($fromBookId); ?> to
                            <?php echo htmlspecialchars($toBookId); ?>
                        </h2>

                        <?php if ($totalBooks > 0) { ?>
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
                            <div>
                                <h4>Total Books: <?php echo $totalBooks; ?></h4>
                            </div>
                        <?php } else { ?>
                            <p>No records found for the given book ID range.</p>
                        <?php } ?>

                        <div class="button-container">
                            <button onclick="window.location.href='report.php'" class="btn btn-primary">Back to Report
                                Page</button>
                            <button onclick="printReport()" class="btn btn-secondary">Print</button>
                            <form method="POST" action="download_book_report.php" target="_blank">
                                <input type="hidden" name="from_book_id"
                                    value="<?php echo htmlspecialchars($fromBookId); ?>">
                                <input type="hidden" name="to_book_id" value="<?php echo htmlspecialchars($toBookId); ?>">
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
    <?php
} else {
    echo "<script type='text/javascript'>alert('Invalid request method.'); window.location.href='report_form.php';</script>";
}
?>