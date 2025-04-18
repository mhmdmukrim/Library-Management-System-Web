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

                    <div class="span9">
                        <form method="post" action="recommendations.php">
                            <table class="table" id="tables">
                                <thead>
                                    <tr>
                                        <th>Book Name</th>
                                        <th>Description</th>
                                        <th>Recommended By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM LMS.recommendations";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $bookname = $row['Book_Name'];
                                        $description = $row['Description'];
                                        $MembershipNo = $row['MembershipNo'];
                                        $R_Id = $row['R_ID']; // Get recommendation ID for deletion
                                
                                        echo "<tr>";
                                        echo "<td>{$bookname}</td>";
                                        echo "<td>{$description}</td>";
                                        echo "<td><b>" . strtoupper($MembershipNo) . "</b></td>";
                                        echo "<td><button type='submit' name='delete_recommendation' value='{$R_Id}' class='btn btn-danger'>Delete</button></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button type="submit" name="delete_all_recommendations" class="btn btn-danger">Delete
                                All</button>
                        </form>
                        <center>
                            <a href="addbook.php" class="btn btn-success">Add a Book</a>
                        </center>
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
        // Handle delete individual recommendation
        if (isset($_POST['delete_recommendation'])) {
            $recommendationId = $_POST['delete_recommendation'];
            $sqlDelete = "DELETE FROM LMS.recommendations WHERE R_Id = ?";
            $stmt = $conn->prepare($sqlDelete);
            $stmt->bind_param("i", $recommendationId);
            if ($stmt->execute()) {
                echo "<script>alert('Recommendation deleted successfully.'); window.location.href='recommendations.php';</script>";
            } else {
                echo "<script>alert('Error deleting recommendation.');</script>";
            }
        }

        // Handle delete all recommendations
        if (isset($_POST['delete_all_recommendations'])) {
            $sqlDeleteAll = "TRUNCATE TABLE LMS.recommendations"; // Use TRUNCATE TABLE for deleting all rows
            if ($conn->query($sqlDeleteAll) === TRUE) {
                echo "<script>alert('All recommendations deleted successfully.'); window.location.href='recommendations.php';</script>";
            } else {
                echo "<script>alert('Error deleting recommendations.');</script>";
            }
        }
        ?>
    </body>

    </html>

<?php } else {
    echo "<script>alert('Access Denied!!!');</script>";
} ?>