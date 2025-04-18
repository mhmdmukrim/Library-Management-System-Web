<?php
require('dbconn.php');
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
                        <form class="form-horizontal row-fluid" action="book.php" method="post">
                            <div class="control-group">
                                <label class="control-label" for="Search"><b>Search:</b></label>
                                <div class="controls">
                                    <input type="text" id="title" name="title" placeholder="Enter Name/ID of Book"
                                        class="span8" required>
                                    <button type="submit" name="submit" class="btn">Search</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <?php
                        if (isset($_POST['submit'])) {
                            $s = $_POST['title'];
                            $sql = "select * from LMS.book where BookId='$s' or Title like '%$s%'";
                        } else
                            $sql = "select * from LMS.book";

                        $result = $conn->query($sql);
                        $rowcount = mysqli_num_rows($result);

                        if (!($rowcount))
                            echo "<br><center><h2><b><i>No Results</i></b></h2></center>";
                        else {


                            ?>
                            <table class="table" id="tables">
                                <thead>
                                    <tr>
                                        <th>Book id</th>
                                        <th>Book name</th>
                                        <th>Availability</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    //$result=$conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $bookid = $row['BookId'];
                                        $name = $row['Title'];
                                        $avail = $row['Availability'];


                                        ?>
                                        <tr>
                                            <td><?php echo $bookid ?></td>
                                            <td><?php echo $name ?></td>
                                            <td><b><?php
                                            if ($avail > 0)
                                                echo "<font color=\"green\">AVAILABLE</font>";
                                            else
                                                echo "<font color=\"red\">NOT AVAILABLE</font>";

                                            ?>

                                                </b></td>
                                            <td>
                                                <center>
                                                    <a href="bookdetails.php?id=<?php echo $bookid; ?>"
                                                        class="btn btn-primary">Details</a>
                                                    <a href="edit_book_details.php?id=<?php echo $bookid; ?>"
                                                        class="btn btn-success">Edit</a>
                                                    <a href="remove_book.php?id=<?php echo $bookid; ?>"
                                                        class="btn btn-danger">Remove</a>
                                                    <a href="generate_book_id_card.php?id=<?php echo $row['BookId']; ?>"
                                                        class="btn btn-info">Generate ID</a>
                                                </center>
                                            </td>
                                        </tr>
                                    <?php }
                        } ?>
                            </tbody>
                        </table>
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