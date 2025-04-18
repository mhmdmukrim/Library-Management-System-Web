<?php
require ('dbconn.php');
?>


<!DOCTYPE html>
<html>

<!-- Head -->

<head>

    <title>Akurana Public Library </title>
    <link rel="icon" href="images/rbg.png" type="image/icon type">

    <!-- Meta-Tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords"
        content="Library Member Login Form Widget Responsive, Login Form Web Template, Flat Pricing Tables, Flat Drop-Downs, Sign-Up Web Templates, Flat Web Templates, Login Sign-up Responsive Web Template, Smartphone Compatible Web Template, Free Web Designs for Nokia, Samsung, LG, Sony Ericsson, Motorola Web Design" />
    <script
        type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <!-- //Meta-Tags -->

    <!-- Style -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all">

    <!-- Fonts -->
    <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <!-- //Fonts -->

</head>
<!-- //Head -->

<!-- Body -->

<body>

    <h1>Akurana Public Library</h1>

    <div class="container">
        
            <div class="login">
                <h2>Sign In</h2>
                <form action="index.php" method="post">
                    <input type="text" Name="MembershipNo" placeholder="MembershipNo" required="">
                    <input type="password" Name="Password" placeholder="Password" required="">


                    <div class="send-button">
                        <!--<form>-->
                        <input type="submit" name="signin" ; value="Sign In">
                </form>
            </div>
        

        <div class="clear"></div>
    </div>

    <div class="clear"></div>

    </div>



    <div class="footer w3layouts agileits">
        <p> &copy; 2024 MHMD MUKRIM. All Rights Reserved </a></p>

    </div>

    <?php
    if (isset($_POST['signin'])) {
        $u = $_POST['MembershipNo'];
        $p = $_POST['Password'];
        $c = $_POST['Category'];

        $sql = "select * from LMS.user where MembershipNo='$u'";

        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $x = $row['Password'];
        $y = $row['Type'];
        if (strcasecmp($x, $p) == 0 && !empty($u) && !empty($p)) {//echo "Login Successful";
            $_SESSION['MembershipNo'] = $u;


            if ($y == 'admin')
                header('location:admin/send_due_reminders.php');
            else
                header('location:student/index.php');

        } else {
            echo "<script type='text/javascript'>alert('Failed to Login! Incorrect MembershipNo or Password')</script>";
        }


    }

    ?>

</body>
<!-- //Body -->

</html>