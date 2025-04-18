<?php
require ('dbconn.php'); // Ensure your database connection is included

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
                        <div class="content">

                            <div class="module">
                                <div class="module-head">
                                    <h3>Add Book</h3>
                                </div>
                                <div class="module-body">
                                    <br>
                                    <form class="form-horizontal row-fluid" action="addbook.php" method="post">

                                        <!-- Add a manual input for Book ID -->
                                        <div class="control-group">
                                            <label class="control-label" for="BookID"><b>Book ID</b></label>
                                            <div class="controls">
                                                <input type="number" id="bookId" name="bookId" placeholder="Enter Book ID"
                                                    class="span8" required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="Title"><b>Book Title</b></label>
                                            <div class="controls">
                                                <input type="text" id="title" name="title" placeholder="Title" class="span8"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="Category"><b>Category</b></label>
                                            <div class="controls">
                                                <select id="category" name="category" class="span8" required>
                                                    <option value="000 - Generalities">Generalities</option>
                                                    <option value="100 - Philosophy & Psychology">Philosophy & Psychology
                                                    </option>
                                                    <option value="200 - Religion">Religion</option>
                                                    <option value="300 - Social Sciences">Social Sciences</option>
                                                    <option value="400 - Language">Language</option>
                                                    <option value="500 - Science">Science</option>
                                                    <option value="600 - Technology">Technology</option>
                                                    <option value="700 - Arts & Recreation">Arts & Recreation</option>
                                                    <option value="800 - Literature">Literature</option>
                                                    <option value="900 - History & Geography">History & Geography</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="ISBN"><b>Book ISBN</b></label>
                                            <div class="controls">
                                                <input type="text" id="ISBN" name="ISBN" placeholder="ISBN" class="span8"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="Author"><b>Author(s)</b></label>
                                            <div class="controls">
                                                <input type="text" id="author1" name="author1" placeholder="Author 1"
                                                    class="span8" required>
                                                <input type="text" id="author2" name="author2" placeholder="Author 2"
                                                    class="span8">
                                                <input type="text" id="author3" name="author3" placeholder="Author 3"
                                                    class="span8">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="Publisher"><b>Publisher</b></label>
                                            <div class="controls">
                                                <input type="text" id="publisher" name="publisher" placeholder="Publisher"
                                                    class="span8" required>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="Year"><b>Year</b></label>
                                            <div class="controls">
                                                <input type="text" id="year" name="year" placeholder="Year" class="span8"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="Price"><b>Price</b></label>
                                            <div class="controls">
                                                <input type="number" id="price" name="price" placeholder="Price"
                                                    class="span8" required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="submit" class="btn">Add Book</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!--/.content-->
                    </div>
                </div>
            </div><!--/.container-->
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
        if (isset($_POST['submit'])) {
            $bookId = $_POST['bookId'];
            $title = $_POST['title'];
            $category = $_POST['category'];
            $ISBN = $_POST['ISBN'];
            $author1 = $_POST['author1'];
            $author2 = $_POST['author2'];
            $author3 = $_POST['author3'];
            $publisher = $_POST['publisher'];
            $year = $_POST['year'];
            $price = $_POST['price'];

            // Prepare and bind SQL statements
            $stmt1 = $conn->prepare("INSERT INTO LMS.book (BookId, Title, Category, ISBN, Publisher, Year, Price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt1->bind_param("isssssi", $bookId, $title, $category, $ISBN, $publisher, $year, $price);

            // Execute statement
            $stmt1->execute();

            // Check if book insertion was successful
            if ($stmt1->affected_rows > 0) {
                // Insert authors
                $stmt2 = $conn->prepare("INSERT INTO LMS.author (BookId, Author) VALUES (?, ?)");
                $stmt2->bind_param("is", $bookId, $author1);
                $stmt2->execute();

                if (!empty($author2)) {
                    $stmt2->bind_param("is", $bookId, $author2);
                    $stmt2->execute();
                }
                if (!empty($author3)) {
                    $stmt2->bind_param("is", $bookId, $author3);
                    $stmt2->execute();
                }

                echo "<script type='text/javascript'>alert('Book added successfully!')</script>";
            } else {
                echo "<script type='text/javascript'>alert('Error adding book!')</script>";
            }

            // Close statements
            $stmt1->close();
            $stmt2->close();
        }
        ?>

    </body>

    </html>

    <?php
} else {
    echo "<script type='text/javascript'>alert('Access Denied!!!')</script>";
}
?>