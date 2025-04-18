<?php
require ('dbconn.php');
?>

<?php
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
                        <div class="content">
                            <div class="module">
                                <div class="module-head">
                                    <h3>Send a message</h3>
                                </div>
                                <div class="module-body">
                                    <form class="form-horizontal row-fluid" action="message.php" method="post">
                                        <div class="control-group">
                                            <label class="control-label" for="MembershipNo"><b>Receiver Membership
                                                    No:</b></label>
                                            <div class="controls">
                                                <input type="text" id="MembershipNo" name="MembershipNo"
                                                    placeholder="Membership No" class="span8">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="Message"><b>Message:</b></label>
                                            <div class="controls">
                                                <input type="text" id="Message" name="Message" placeholder="Enter Message"
                                                    class="span8" required>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="submit" class="btn">Send Message</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="module">
                                <div class="module-head">
                                    <h3>Received Messages</h3>
                                </div>
                                <div class="module-body">
                                    <form method="post" action="message.php">
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
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        require 'footer.php';
        ?>


        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
        <script src="scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
        <script src="scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="scripts/common.js" type="text/javascript"></script>

        <?php
        if (isset($_POST['submit'])) {
            $MembershipNo = $_POST['MembershipNo'];
            $message = $_POST['Message'];
            $senderMembershipNo = $_SESSION['MembershipNo'];

            // If MembershipNo is empty, send message to all students
            if (empty($MembershipNo)) {
                $sql = "SELECT MembershipNo FROM LMS.user";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $studentMembershipNo = $row['MembershipNo'];

                        // Insert message for each student
                        $sqlInsert = "INSERT INTO LMS.message (MembershipNo, SMembershipNo, Msg, Date, Time) VALUES ('$studentMembershipNo', '$senderMembershipNo', '$message', CURDATE(), CURTIME())";
                        $conn->query($sqlInsert);
                    }
                    echo "<script type='text/javascript'>alert('Message sent to all Members.')</script>";
                } else {
                    echo "<script type='text/javascript'>alert('No Members found.')</script>";
                }
            } else {
                // Insert message for specific student
                $sqlInsert = "INSERT INTO LMS.message (MembershipNo, SMembershipNo, Msg, Date, Time) VALUES ('$MembershipNo', '$senderMembershipNo', '$message', CURDATE(), CURTIME())";

                if ($conn->query($sqlInsert) === TRUE) {
                    echo "<script type='text/javascript'>alert('Message sent.')</script>";
                } else {
                    echo "<script type='text/javascript'>alert('Error sending message.')</script>";
                }
            }
        }

        // Handle message deletion
        if (isset($_POST['delete'])) {
            $msgID = $_POST['delete'];
            $sqlDelete = "DELETE FROM LMS.message WHERE M_Id = '$msgID'";
            if ($conn->query($sqlDelete) === TRUE) {
                echo "<script type='text/javascript'>alert('Message deleted successfully.'); window.location.href='message.php';</script>";
            } else {
                echo "<script type='text/javascript'>alert('Error deleting message.');</script>";
            }
        }

        // Handle deletion of all messages
        if (isset($_POST['delete_all'])) {
            $sqlDeleteAll = "DELETE FROM LMS.message WHERE MembershipNo = 'ADMIN'";
            if ($conn->query($sqlDeleteAll) === TRUE) {
                echo "<script type='text/javascript'>alert('All messages deleted successfully.'); window.location.href='message.php';</script>";
            } else {
                echo "<script type='text/javascript'>alert('Error deleting messages.');</script>";
            }
        }
        ?>
    </body>

    </html>

<?php } else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
} ?>