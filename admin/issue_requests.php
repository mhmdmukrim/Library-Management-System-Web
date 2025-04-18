<?php
require ('dbconn.php');

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
                    <div class="span9">
                        <center>
                            <a href="issue_requests.php" class="btn btn-info">Issue Books</a>
                            <a href="renew_requests.php" class="btn btn-info">Renew Books</a>
                            <a href="return_requests.php" class="btn btn-info">Return Books</a>

                            <h1><i>Issue Books</i></h1>



                            <form action="issue_books.php" method="post">
                                <div class="form-group">
                                    <label for="membershipNo">Membership Number</label>
                                    <input type="text" name="membershipNo" id="membershipNo" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="bookId">Book ID</label>
                                    <input type="text" name="bookId" id="bookId" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>

                        </center>

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

    <?php
} else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
}
?>