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

                            <div class="module">
                                <div class="module-head">
                                    <h3>Member Details</h3>
                                </div>
                                <div class="module-body">
                                    <?php
                                    $rno = $_GET['id'];
                                    $sql = "select * from LMS.user where MembershipNo='$rno'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();

                                    $name = $row['Name'];
                                    $category = $row['Category'];
                                    $email = $row['EmailId'];
                                    $mobno = $row['MobNo'];


                                    echo "<b><u>Name:</u></b> " . $name . "<br><br>";
                                    echo "<b><u>Category:</u></b> " . $category . "<br><br>";
                                    echo "<b><u>Roll No:</u></b> " . $rno . "<br><br>";
                                    echo "<b><u>Email Id:</u></b> " . $email . "<br><br>";
                                    echo "<b><u>Mobile No:</u></b> " . $mobno . "<br><br>";
                                    ?>

                                    <a href="student.php" class="btn btn-primary">Go Back</a>
                                </div>
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

    </body>

    </html>


<?php } else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
} ?>