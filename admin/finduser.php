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
                    <!--/.span3-->
                    <div class="span9">
                        <div class="content">
                            <div class="btn-controls">
                                <div class="btn-box-row row-fluid">
                                    <a href="findbook.php" class="btn-box big span4"><i class=" icon-search"></i><b>Find
                                            Book</b>
                                    </a><a href="findbookissue.php" class="btn-box big span4"><i
                                            class="icon-book"></i><b>Find Book Issue</b>
                                    </a><a href="finduser.php" class="btn-box big span4"><i class="icon-user"></i><b>Find
                                            User</b>
                                    </a>
                                </div>
                            </div>
                            <!--/.module-->
                        </div>
                        <!--/.content-->
                    </div>
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