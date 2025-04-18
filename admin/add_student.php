<?php
require ('dbconn.php');

if (!isset($_SESSION['MembershipNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
    exit();
}

require 'vendor/autoload.php'; // Load PHPMailer

function generateRandomPassword($length = 8)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

function sendEmail($email, $membershipNo, $password)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'mukrimmhmd@gmail.com'; // SMTP username
        $mail->Password = 'iguk mpfl rjot boxw'; // SMTP password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('mukrimmhmd@gmail.com', 'Akurana Public Library');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Akurana Public Library';
        $mail->Body = "Dear Member,<br><br>Your account has been created successfully.<br><br>Membership Number: $membershipNo<br>Password: $password<br><br>Please change your password after logging in for the first time.<br><br>Login Through https://akulms.x10.mx/<br><br>Best regards,<br>Akurana Public Library";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function sendExistingCredentials($email, $membershipNo, $password)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'mukrimmhmd@gmail.com'; // SMTP username
        $mail->Password = 'iguk mpfl rjot boxw'; // SMTP password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('mukrimmhmd@gmail.com', 'Akurana Public Library');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Existing Credentials at Akurana Public Library';
        $mail->Body = "Dear Member,<br><br>Your account already exists with the following credentials.<br><br>Membership Number: $membershipNo<br>Password: $password<br><br>Login Through https://akulms.x10.mx/<br><br>Best regards,<br>Akurana Public Library";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
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
                    <h2>Add Member</h2>
                    <form action="add_student.php" method="post">
                        <input type="text" name="Name" placeholder="Name" required>
                        <input type="text" name="Email" placeholder="Email" required>
                        <input type="text" name="PhoneNumber" placeholder="Phone Number">
                        <input type="text" name="MembershipNo" placeholder="Membership Number" required>
                        <select name="Category" id="Category">
                            <option value="GEN">User</option>
                        </select>
                        <br><br>
                        <div class="send-button">
                            <input type="submit" name="add_student" value="Add Member">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    require 'footer.php';
    ?>

    <?php
    if (isset($_POST['add_student'])) {
        $name = $_POST['Name'];
        $email = $_POST['Email'];
        $mobno = $_POST['PhoneNumber'];
        $MembershipNo = $_POST['MembershipNo'];
        $category = $_POST['Category'];
        $type = 'Member';

        // Check if MembershipNo already exists
        $sql_check = "SELECT * FROM LMS.user WHERE MembershipNo='$MembershipNo'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // Fetch existing credentials
            $row = $result_check->fetch_assoc();
            $existingEmail = $row['EmailId'];
            $existingPassword = $row['Password'];

            // Send existing credentials
            if (sendExistingCredentials($existingEmail, $MembershipNo, $existingPassword)) {
                echo "<script type='text/javascript'>alert('Member Exists. Existing credentials have been emailed.')</script>";
            } else {
                echo "<script type='text/javascript'>alert('Member Exists. Email Sending Failed.')</script>";
            }
        } else {
            // Generate a random password
            $password = generateRandomPassword();

            $sql = "INSERT INTO LMS.user (Name, Type, Category, MembershipNo, EmailId, MobNo, Password) VALUES ('$name', '$type', '$category', '$MembershipNo', '$email', '$mobno', '$password')";

            if ($conn->query($sql) === TRUE) {
                // Send email
                if (sendEmail($email, $MembershipNo, $password)) {
                    echo "<script type='text/javascript'>alert('Member Added Successfully. Email Sent.')</script>";
                } else {
                    echo "<script type='text/javascript'>alert('Member Added Successfully. Email Sending Failed.')</script>";
                }
            } else {
                echo "<script type='text/javascript'>alert('Error Adding Member.')</script>";
            }
        }
    }
    ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="scripts/common.js" type="text/javascript"></script>

</body>

</html>