<?php
require('dbconn.php');

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
        <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
        <style>
            .navbar {
                margin-bottom: 20px;
            }
            .container-form {
                margin-top: 20px;
            }
            form {
                background: #f9f9f9;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            label {
                margin-right: 10px;
            }
            input[type="date"], input[type="text"] {
                margin-bottom: 10px;
                padding: 5px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            input[type="submit"] {
                margin-top: 10px;
                padding: 10px 15px;
                background-color: #5bc0de;
                border: none;
                color: white;
                border-radius: 4px;
                cursor: pointer;
            }
            input[type="submit"]:hover {
                background-color: #31b0d5;
            }
            .footer {
                padding: 10px 0;
                background: #f1f1f1;
                text-align: center;
            }
        </style>
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
                        <div class="container-form">
                            <form method="POST" action="trf_reports.php">
                                <label for="from_date">From Date:</label>
                                <input type="date" id="from_date" name="from_date" required>
                                <br>
                                <label for="to_date">To Date:</label>
                                <input type="date" id="to_date" name="to_date" required>
                                <br>
                                <input type="submit" value="Generate Report">
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
    echo "<script type='text/javascript'>alert('Access Denied!'); window.location.href='login.php';</script>";
}
?>
