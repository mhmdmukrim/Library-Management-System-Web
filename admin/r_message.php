<?php
require ('dbconn.php');


if (isset($_SESSION['MembershipNo'])) {
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
                                <h3>Received Messages</h3>
                            </div>
                            <div class="module-body">
                                <form method="post" action="r_message.php">
                                    <table class="table" id="tables">
                                        <thead>
                                            <tr>
                                                <th>Sender Membership No</th>
                                                <th>Message</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM LMS.message WHERE MembershipNo = 'ADMIN' ORDER BY Date DESC, Time DESC";
                                            $result = $conn->query($sql);
                                            while ($row = $result->fetch_assoc()) {
                                                $id = $row['M_Id'];
                                                $sender = $row['SMembershipNo'];
                                                $msg = $row['Msg'];
                                                $date = $row['Date'];
                                                $time = $row['Time'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $sender ?></td>
                                                    <td><?php echo $msg ?></td>
                                                    <td><?php echo $date ?></td>
                                                    <td><?php echo $time ?></td>
                                                    <td>
                                                        <button type="submit" name="delete" value="<?php echo $id ?>"
                                                            class="btn btn-danger">Delete</button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="delete_all" class="btn btn-danger">Delete All
                                        Messages</button>
                                </form>
                                <form action="message.php" method="post">
                                    <button type="submit" name="send" class="btn btn-primary">Send Messages</button>
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
    </body>

    </html>
    <?php
} else {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='login.php';</script>";
}

// Handle message deletion
if (isset($_POST['delete'])) {
    $msgID = $_POST['delete'];
    $sqlDelete = "DELETE FROM LMS.message WHERE M_Id = '$msgID'";
    if ($conn->query($sqlDelete) === TRUE) {
        echo "<script type='text/javascript'>alert('Message deleted successfully.'); window.location='r_message.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Error deleting message.'); window.location='r_message.php';</script>";
    }
}

// Handle deletion of all messages
if (isset($_POST['delete_all'])) {
    $sqlDeleteAll = "DELETE FROM LMS.message WHERE MembershipNo = 'ADMIN'";
    if ($conn->query($sqlDeleteAll) === TRUE) {
        echo "<script type='text/javascript'>alert('All messages deleted successfully.'); window.location='r_message.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Error deleting all messages.'); window.location='r_message.php';</script>";
    }
}
?>