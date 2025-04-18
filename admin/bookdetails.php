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

                    <div class="span9">
                        <div class="content">

                            <div class="module">
                                <div class="module-head">
                                    <h3>Book Details</h3>
                                </div>
                                <div class="module-body">
                                    <?php
                                    $x = $_GET['id'];
                                    $sql = "select * from LMS.book where BookId='$x'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();

                                    $bookid = $row['BookId'];
                                    $name = $row['Title'];
                                    $category = $row['Category'];
                                    $ISBN = $row['ISBN'];
                                    $publisher = $row['Publisher'];
                                    $price = $row['Price'];
                                    $year = $row['Year'];

                                    echo "<b>Book ID:</b> " . $bookid . "<br><br>";
                                    echo "<b>Title:</b> " . $name . "<br><br>";
                                    echo "<b>Category:</b> " . $category . "<br><br>";
                                    echo "<b>ISBN:</b> " . $ISBN . "<br><br>";
                                    $sql1 = "select * from LMS.author where BookId='$bookid'";
                                    $result = $conn->query($sql1);

                                    echo "<b>Author:</b> ";
                                    while ($row1 = $result->fetch_assoc()) {
                                        echo $row1['Author'] . "&nbsp;";
                                    }
                                    echo "<br><br>";
                                    echo "<b>Publisher:</b> " . $publisher . "<br><br>";
                                    echo "<b>Price (LKR):</b> " . $price . "<br><br>";
                                    echo "<b>Year:</b> " . $year . "<br><br>";




                                    ?>

                                    <a href="book.php" class="btn btn-primary">Go Back</a>
                                </div>
                            </div>
                        </div>
                        <!--/.span3-->
                        <!--/.span9-->

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