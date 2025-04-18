<?php
ob_start();
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

                    <div class="span9">
                        <div class="module">
                            <div class="module-head">
                                <h3>Update Book Details</h3>
                            </div>
                            <div class="module-body">

                                <?php
                                $bookid = $_GET['id'];
                                $sql = "select * from LMS.book where Bookid='$bookid'";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                $name = $row['Title'];
                                $ISBN = $row['ISBN'];
                                $publisher = $row['Publisher'];
                                $year = $row['Year'];
                                $avail = $row['Availability'];


                                ?>

                                <br>
                                <form class="form-horizontal row-fluid"
                                    action="edit_book_details.php?id=<?php echo $bookid ?>" method="post">
                                    <div class="control-group">
                                        <b>
                                            <label class="control-label" for="Title">Book Title:</label></b>
                                        <div class="controls">
                                            <input type="text" id="Title" name="Title" value="<?php echo $name ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <b>
                                            <label class="control-label" for="ISBN">Book ISBN:</label></b>
                                        <div class="controls">
                                            <input type="text" id="ISBN" name="ISBN" value="<?php echo $ISBN ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <b>
                                            <label class="control-label" for="Publisher">Publisher:</label></b>
                                        <div class="controls">
                                            <input type="text" id="Publisher" name="Publisher"
                                                value="<?php echo $publisher ?>" class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <b>
                                            <label class="control-label" for="Year">Year:</label></b>
                                        <div class="controls">
                                            <input type="text" id="Year" name="Year" value="<?php echo $year ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <!-- <div class="control-group">
                                        <b>
                                            <label class="control-label" for="Availability">Availability:</label></b>
                                        <div class="controls">
                                            <input type="text" id="Availability" name="Availability"
                                                value="<?php echo $avail ?>" class="span8" required>
                                        </div>
                                    </div> -->

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn">Update Details</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
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

        <?php
        if (isset($_POST['submit'])) {
            $bookid = $_GET['id'];
            $name = $_POST['Title'];
            $ISBN = $_POST['ISBN'];
            $publisher = $_POST['Publisher'];
            $year = $_POST['Year'];
            // $avail = $_POST['Availability'];
    
            $sql1 = "update LMS.book set Title='$name', ISBN='$ISBN', Publisher='$publisher', Year='$year' where BookId='$bookid'";



            if ($conn->query($sql1) === TRUE) {
                echo "<script type='text/javascript'>alert('Success')</script>";
                header("Refresh:0.01; url=book.php", true, 303);
            } else {//echo $conn->error;
                echo "<script type='text/javascript'>alert('Error')</script>";
            }
        }
        ?>

    </body>

    </html>


<?php } else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
} ?>