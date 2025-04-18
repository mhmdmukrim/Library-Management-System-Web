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
                    <!--/.span3-->

                    <div class="span9">
                        <div class="module">
                            <div class="module-head">
                                <h3>Update Details</h3>
                            </div>
                            <div class="module-body">


                                <?php
                                $MembershipNo = $_SESSION['MembershipNo'];
                                $sql = "select * from LMS.user where MembershipNo='$MembershipNo'";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();

                                $name = $row['Name'];
                                $email = $row['EmailId'];
                                $mobno = $row['MobNo'];
                                $pswd = $row['Password'];
                                ?>

                                <form class="form-horizontal row-fluid"
                                    action="edit_admin_details.php?id=<?php echo $MembershipNo ?>" method="post">

                                    <div class="control-group">
                                        <label class="control-label" for="Name"><b>Name:</b></label>
                                        <div class="controls">
                                            <input type="text" id="Name" name="Name" value="<?php echo $name ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="EmailId"><b>Email Id:</b></label>
                                        <div class="controls">
                                            <input type="text" id="EmailId" name="EmailId" value="<?php echo $email ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="MobNo"><b>Mobile Number:</b></label>
                                        <div class="controls">
                                            <input type="text" id="MobNo" name="MobNo" value="<?php echo $mobno ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Password"><b>New Password:</b></label>
                                        <div class="controls">
                                            <input type="password" id="Password" name="Password" value="<?php echo $pswd ?>"
                                                class="span8" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn-primary">
                                                <center>Update Details</center>
                                            </button>
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
            $MembershipNo = $_GET['id'];
            $name = $_POST['Name'];
            $email = $_POST['EmailId'];
            $mobno = $_POST['MobNo'];
            $pswd = $_POST['Password'];

            $sql1 = "update LMS.user set Name='$name', EmailId='$email', MobNo='$mobno', Password='$pswd' where MembershipNo='$MembershipNo'";



            if ($conn->query($sql1) === TRUE) {
                echo "<script type='text/javascript'>alert('Success')</script>";
                header("Refresh:0.01; url=index.php", true, 303);
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