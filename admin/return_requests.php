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
                            <a href="issue_requests.php" class="btn btn-info">Issue Books</a>
                            <a href="renew_requests.php" class="btn btn-info">Renew Books</a>
                            <a href="return_requests.php" class="btn btn-info">Return Books</a>
                            <h1><i>Return Books</i></h1>

                            <form method="post" action="">
                                <input type="text" name="book_id" placeholder="Book ID" required>
                                <button type="submit" name="search">Search</button>
                            </form>

                            <?php
                            if (isset($_POST['search'])) {
                                $book_id = $_POST['book_id'];

                                // Fetch user's lending books that haven't been returned yet
                                $sql = "SELECT record.BookId, record.MembershipNo, book.Title, record.due_date, record.record_id,
                                record.Dues + record.PrDues AS TotalDues
                                FROM record 
                                JOIN book ON record.BookId = book.BookId 
                                WHERE record.BookId = ? AND record.Date_of_Return IS NULL";

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $book_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    echo '<table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Membership Number</th>
                                                    <th>Book Id</th>
                                                    <th>Book Name</th>
                                                    <th>Due Date</th>
                                                    <th>Dues</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                    while ($row = $result->fetch_assoc()) {
                                        $bookid = $row['BookId'];
                                        $membershipNo = $row['MembershipNo'];
                                        $name = $row['Title'];
                                        $due_date = $row['due_date'];
                                        $dues = $row['TotalDues'];
                                        $record_id = $row['record_id']; // Get the record_id
                        
                                        echo "<tr>
                                                <td>" . strtoupper($membershipNo) . "</td>
                                                <td>" . $bookid . "</td>
                                                <td><b>" . $name . "</b></td>
                                                <td>" . $due_date . "</td>
                                                <td>" . ($dues ?? 'None') . "</td>
                                                <td>
                                                    <form action='return_books.php' method='POST'>
                                                        <input type='hidden' name='book_id' value='$bookid'>
                                                        <input type='hidden' name='membership_no' value='$membershipNo'>
                                                        <input type='hidden' name='record_id' value='$record_id'>
                                                        <button type='submit' name='return' class='btn btn-success'>Return</button>
                                                    </form>
                                                </td>
                                              </tr>";
                                    }
                                    echo '</tbody></table>';
                                } else {
                                    echo '<div class="alert alert-warning">No books found for this user.</div>';
                                }
                            }
                            ?>
                        </center>
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
    </body>

    </html>

<?php } else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
}
?>