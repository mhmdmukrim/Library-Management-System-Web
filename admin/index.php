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
                            <div class="card" style="width: 50%;">
                                <img class="card-img-top" src="images/profile2.png" alt="Card image cap">
                                <div class="card-body">
                                    <?php
                                    $MembershipNo = $_SESSION['MembershipNo'];
                                    $sql = "select * from LMS.user where MembershipNo='$MembershipNo'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();

                                    $name = $row['Name'];
                                    $category = $row['Category'];
                                    $email = $row['EmailId'];
                                    $mobno = $row['MobNo'];

                                    // Calculate total paid dues
                                    $duesSql = "SELECT SUM(PaidDues) AS PaidDues FROM LMS.record";
                                    $duesResult = $conn->query($duesSql);
                                    if ($duesResult->num_rows > 0) {
                                        $duesRow = $duesResult->fetch_assoc();
                                        $totalPaidDues = $duesRow['PaidDues'];
                                    }
                                    ?>
                                    <i>
                                        <h1 class="card-title">
                                            <center><?php echo $name ?></center>
                                        </h1>
                                        <br>
                                        <p><b>Email ID: </b><?php echo $email ?></p>
                                        <br>
                                        <p><b>Mobile number: </b><?php echo $mobno ?></p>
                                        <br>
                                        <p><b>Total Amount: </b><?php echo $totalPaidDues ?></p>
                                    </i>
                                </div>
                            </div>
                            <br>
                            <a href="edit_admin_details.php" class="btn btn-primary">Edit Details</a>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <?php
        require 'footer.php';
        ?>

        <script>
            document.getElementById('reportForm').onsubmit = function () {
                window.open('', 'reportPopup', 'width=600,height=400');
            };
        </script>
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