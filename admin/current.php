<?php
require ('dbconn.php');

// Fee rate per day
$feeRatePerDay = 1; // LKR 1 per day overdue

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
                        <form class="form-horizontal row-fluid" action="current.php" method="post">
                            <div class="control-group">
                                <label class="control-label" for="Search"><b>Search:</b></label>
                                <div class="controls">
                                    <input type="text" id="title" name="title"
                                        placeholder="Enter Roll No of Member/Book Name/Book Id." class="span8" required>
                                    <button type="submit" name="submit" class="btn">Search</button>
                                </div>
                            </div>
                        </form>
                        <!-- <form action="send_due_reminders.php " method="post">
                            <button type="submit" name="submit" class="btn btn-primary">Send Reminders</button>
                        </form> -->
                        <br>

                        <?php
                        if (isset($_POST['submit'])) {
                            $s = $_POST['title'];
                            $sql = "SELECT record.BookId, MembershipNo, Title, Date_of_Issue, Due_Date, Date_of_Return, Dues, PrDues, PaidDues, Renewals, DATEDIFF(CURDATE(), Due_Date) AS overdueDays 
                                FROM LMS.record 
                                JOIN LMS.book ON book.Bookid = record.BookId 
                                WHERE Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL 
                                AND (MembershipNo='$s' OR record.BookId='$s' OR Title LIKE '%$s%')";
                        } else {
                            $sql = "SELECT record.BookId, MembershipNo, Title, Date_of_Issue, Due_Date, Date_of_Return, Dues, PrDues, PaidDues, Renewals, DATEDIFF(CURDATE(), Due_Date) AS overdueDays 
                                FROM LMS.record 
                                JOIN LMS.book ON book.Bookid = record.BookId 
                                WHERE Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL";
                        }
                        $result = $conn->query($sql);
                        $rowcount = mysqli_num_rows($result);

                        if (!$rowcount) {
                            echo "<br><center><h2><b><i>No Results</i></b></h2></center>";
                        } else {
                            ?>
                            <table class="table" id="tables">
                                <thead>
                                    <tr>
                                        <th>Membership No</th>
                                        <th>Book id</th>
                                        <th>Book name</th>
                                        <th>Issue Date</th>
                                        <th>Due date</th>
                                        <th>Renewals</th>
                                        <th>Dues (LKR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $MembershipNo = $row['MembershipNo'];
                                        $bookid = $row['BookId'];
                                        $name = $row['Title'];
                                        $issuedate = $row['Date_of_Issue'];
                                        $duedate = $row['Due_Date'];
                                        $renewals = $row['Renewals'];
                                        $previousDues = $row['PrDues'];
                                        $paidDues = $row['PaidDues'];
                                        $overdueDays = $row['overdueDays'];

                                        // Calculate overdue amount
                                        $overdueAmount = max(0, $overdueDays * $feeRatePerDay);
                                        $totalDues = max(0, $previousDues + $overdueAmount - $paidDues);

                                        echo "<tr>";
                                        echo "<td>" . strtoupper($MembershipNo) . "</td>";
                                        echo "<td>" . $bookid . "</td>";
                                        echo "<td>" . $name . "</td>";
                                        echo "<td>" . $issuedate . "</td>";
                                        echo "<td>" . $duedate . "</td>";
                                        echo "<td>" . $renewals . "</td>";
                                        echo "<td>";
                                        if ($totalDues > 0) {
                                            echo "<font color='red'>" . $totalDues . "</font>";
                                        } else {
                                            echo "<font color='green'>0</font>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        ?>
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
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
}
?>