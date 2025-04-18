<?php
require ('dbconn.php');
?>

<?php
if ($_SESSION['MembershipNo']) {
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <?php
    require 'head.php';
    ?>

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
                    <br>
                    <br>
                    <br>
                    <div class="span3">
                        <center>
                            <a href="issue_requests.php" class="btn btn-info">
                                <image width="100px" src="images/book2.png"></image>
                                <p>Issue Books</p>
                            </a>
                        </center>
                    </div>
                    <div class="span3">
                        <center>
                            <a href="renew_requests.php" class="btn btn-info">
                                <image width="100px" src="images/book3.png"></image>
                                <p>Renew Books</p>
                            </a>
                        </center>
                    </div>
                    <div class="span3">
                        <center>
                            <a href="return_requests.php" class="btn btn-info">
                                <image width="100px" src="images/book4.png"></image>
                                <p>Return Books</p>
                            </a>
                        </center>
                    </div>
                    <!--/.span3-->
                    <!--/.span9-->
                </div>
            </div>
            <!--/.container-->
        </div>
        <?php
        require 'footer.php';
        ?>


        <!--/.wrapper-->
        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
        <script src="scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="scripts/common.js" type="text/javascript"></script>

    </body>

    </html>


<?php } else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
} ?>