<?php
require ('dbconn.php');

if (isset($_SESSION['MembershipNo'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Akurana Public Library</title>
        <link rel="icon" href="images/rbg.png" type="image/icon type">
        <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
        <link type="text/css" href="css/theme.css" rel="stylesheet">
        <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
        <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
            rel='stylesheet'>
        <style>
            .navbar {
                margin-bottom: 20px;
            }

            .sidebar {
                margin-top: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                padding: 10px;
                text-align: left;
                border: 1px solid #ddd;
            }

            th {
                background-color: #f4f4f4;
            }

            .button-container {
                margin: 20px 0;
                text-align: center;
            }

            .footer {
                padding: 10px 0;
                background: #f1f1f1;
                text-align: center;
            }
        </style>
        <script type="text/javascript">
            function openReportPopup(reportType) {
                const popup = window.open("", "ReportPopup", "width=800,height=600");

                const xhr = new XMLHttpRequest();
                xhr.open("POST", reportType, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        popup.document.open();
                        popup.document.write(xhr.responseText);
                        popup.document.close();
                    }
                };

                xhr.send();
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

                    <div class="span9">
                        <div class="button">
                            <br>
                            <form action="audit_reports.php" id="reportForm" method="POST">
                                <input class="btn btn-success" type="submit" value="Audit Report (Date)">
                            </form>
                            <br>
                            <form action="book_report.php" id="reportForm" method="POST">
                                <input class="btn btn-danger" type="submit" value="Book Report (BookId)">
                            </form>
                            <br>
                            <form action="all_books.php" id="reportForm" method="POST">
                                <input class="btn btn-primary" type="submit" value="All Books">
                            </form>
                            <br>
                            <form action="trf_sum.php" method="post">
                                <input class="btn btn-success" type="submit" value="Transfer Amount Summery">
                            </form>
                            <br>
                            <button class="btn btn-primary" onclick="openReportPopup('transfer_report.php')">Transfer
                                Amount</button>
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
    echo "<script type='text/javascript'>alert('Access Denied!'); window.location.href='login.php';</script>";
}
?>