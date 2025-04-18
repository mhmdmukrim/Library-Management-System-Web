<?php
ob_start();
require ('dbconn.php'); // Ensure you have your database connection

if ($_SESSION['MembershipNo']) {
    // Assuming you pass MembershipNo via GET
    if (isset($_GET['id'])) {
        $MembershipNo = $_GET['id'];

        // Retrieve current student details
        $query = "SELECT * FROM LMS.user WHERE MembershipNo = '$MembershipNo'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row['Name'];
            $type = $row['Type'];
            $category = $row['Category'];
            $email = $row['EmailId'];
            $mobNo = $row['MobNo'];
            $password = $row['Password']; // Assuming you store passwords securely

            // Handle form submission for updating details
            if (isset($_POST['submit'])) {
                // Collect updated values from the form
                $name = $_POST['Name'];
                $type = $_POST['Type'];
                $category = $_POST['Category'];
                $email = $_POST['EmailId'];
                $mobNo = $_POST['MobNo'];
                $password = $_POST['Password']; // Handle password securely

                // Update query
                $updateQuery = "UPDATE LMS.user SET Name='$name', Type='$type', Category='$category', EmailId='$email', MobNo='$mobNo', Password='$password' WHERE MembershipNo='$MembershipNo'";

                if ($conn->query($updateQuery) === TRUE) {
                    echo "<script>alert('Member details updated successfully');</script>";
                    header("Refresh:0.01; url=student.php", true, 303); // Redirect to student management page
                    exit();
                } else {
                    echo "<script>alert('Error updating details: " . $conn->error . "');</script>";
                }
            }
        } else {
            echo "<script>alert('Member not found');</script>";
        }
    } else {
        echo "<script>alert('Invalid request');</script>";
    }
} else {
    echo "<script>alert('Access Denied!!!');</script>";
}
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
                            <h3>Edit Member Details</h3>
                        </div>
                        <div class="module-body">
                            <form class="form-horizontal row-fluid" action="" method="post">
                                <div class="control-group">
                                    <label class="control-label" for="Name">Name:</label>
                                    <div class="controls">
                                        <input type="text" id="Name" name="Name" value="<?php echo $name ?>"
                                            class="span8" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="Type">Type:</label>
                                    <div class="controls">
                                        <select id="Type" name="Type" class="span8">
                                            <option value="" <?php if ($type == '')
                                                echo 'selected'; ?>>
                                            </option>
                                            <option value="admin" <?php if ($type == 'admin')
                                                echo 'selected'; ?>>
                                                Admin</option>
                                            <option value="user" <?php if ($type == 'user')
                                                echo 'selected'; ?>>
                                                User</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="Category">Category:</label>
                                    <div class="controls">
                                        <input type="text" id="Category" name="Category" value="<?php echo $category ?>"
                                            class="span8" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="EmailId">Email ID:</label>
                                    <div class="controls">
                                        <input type="email" id="EmailId" name="EmailId" value="<?php echo $email ?>"
                                            class="span8" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="MobNo">Mobile Number:</label>
                                    <div class="controls">
                                        <input type="text" id="MobNo" name="MobNo" value="<?php echo $mobNo ?>"
                                            class="span8" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="Password">Password:</label>
                                    <div class="controls">
                                        <input type="password" id="Password" name="Password"
                                            value="<?php echo $password ?>" class="span8" required>
                                    </div>
                                </div>

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
</body>

</html>