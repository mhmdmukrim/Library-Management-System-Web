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
                    <!--/.span3-->

                    <div class="span9">
                        <form class="form-horizontal row-fluid" action="student.php" method="post">
                            <div class="control-group">
                                <label class="control-label" for="Search"><b>Search:</b></label>
                                <div class="controls">
                                    <input type="text" id="title" name="title" placeholder="Enter Name/Roll No of Member"
                                        class="span8" required>
                                    <button type="submit" name="submit" class="btn">Search</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <?php
                        if (isset($_POST['submit'])) {
                            $s = $_POST['title'];
                            $sql = "select * from LMS.user where (MembershipNo='$s' or Name like '%$s%') and MembershipNo<>'ADMIN'";
                        } else
                            $sql = "select * from LMS.user where MembershipNo<>'ADMIN'";

                        $result = $conn->query($sql);
                        $rowcount = mysqli_num_rows($result);

                        if (!($rowcount))
                            echo "<br><center><h2><b><i>No Results</i></b></h2></center>";
                        else {


                            ?>
                            <table class="table" id="tables">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Roll No.</th>
                                        <th>Email id</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    //$result=$conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {

                                        $email = $row['EmailId'];
                                        $name = $row['Name'];
                                        $MembershipNo = $row['MembershipNo'];
                                        ?>
                                        <tr>
                                            <td><?php echo $name ?></td>
                                            <td><?php echo $MembershipNo ?></td>
                                            <td><?php echo $email ?></td>
                                            <td>
                                                <center>
                                                    <a href="studentdetails.php?id=<?php echo $MembershipNo; ?>"
                                                        class="btn btn-success">Details</a>
                                                    <a href="edit_student_details.php?id=<?php echo $MembershipNo; ?>"
                                                        class="btn btn-success">Edit</a>
                                                    <a href="remove_student.php?id=<?php echo $MembershipNo; ?>"
                                                        class="btn btn-danger">Remove</a>
                                                    <a href="generate_id_card.php?id=<?php echo $MembershipNo; ?>"
                                                        class="btn btn-primary">Generate id</a>
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