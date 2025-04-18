<?php
require ('dbconn.php');
require_once ('vendor/autoload.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from_book_id = $_POST['from_book_id'];
    $to_book_id = $_POST['to_book_id'];
    $membershipNo = isset($_POST['membership_no']) ? $_POST['membership_no'] : '';
    $fileFormat = $_POST['file_format'];

    $query = "SELECT b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price, GROUP_CONCAT(a.Author SEPARATOR ', ') as Authors
    FROM book b
    LEFT JOIN author a ON b.BookId = a.BookId
    WHERE b.BookId BETWEEN ? AND ?
    GROUP BY b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price";

    if (!empty($membershipNo)) {
        $query .= " AND r.MembershipNo = ?";
    }

    $stmt = $conn->prepare($query);
    if (!empty($membershipNo)) {
        $stmt->bind_param("sss", $from_book_id, $to_book_id, $membershipNo);
    } else {
        $stmt->bind_param("ss", $from_book_id, $to_book_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Calculate totals
    $totalBooks = $result->num_rows;

    $result->data_seek(0); // Reset pointer to the beginning of the result set

    $formattedfrom_book_id = $from_book_id;
    $formattedto_book_id = $to_book_id;
    $fileName = "Book_report_{$formattedfrom_book_id}-{$formattedto_book_id}";

    switch ($fileFormat) {
        case 'pdf':
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Akurana Public Library');
            $pdf->SetTitle('Book Report');
            $pdf->SetSubject('Book Report from ' . $from_book_id . ' to ' . $to_book_id);
            $pdf->SetKeywords('Book Report, Library');

            $pdf->SetHeaderData('', 0, 'Book Report', 'From: ' . $from_book_id . ' To: ' . $to_book_id);
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 12);

            $html = '<h1>Book Report</h1>';
            $html .= '<table border="1" cellspacing="3" cellpadding="4">
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>ISBN</th>
                                <th>Publisher</th>
                                <th>Year</th>
                                <th>Price (LKR)</th>
                                <th>Authors</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                            <td>' . $row['BookId'] . '</td>
                            <td>' . $row['Title'] . '</td>
                            <td>' . $row['Category'] . '</td>
                            <td>' . $row['ISBN'] . '</td>
                            <td>' . $row['Publisher'] . '</td>
                            <td>' . $row['Year'] . '</td>
                            <td>' . $row['Price'] . '</td>
                            <td>' . $row['Authors'] . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
            $html .= '<h3>Total Books: ' . $totalBooks . '</h3>';
            $pdf->writeHTML($html);
            $pdf->Output($fileName . '.pdf', 'D');
            exit;

        case 'doc':
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $section->addText('Book Report from ' . $from_book_id . ' to ' . $to_book_id);
            $table = $section->addTable();
            $table->addRow();
            $table->addCell()->addText('Book ID');
            $table->addCell()->addText('Title');
            $table->addCell()->addText('Category');
            $table->addCell()->addText('ISBN');
            $table->addCell()->addText('Publisher');
            $table->addCell()->addText('Year');
            $table->addCell()->addText('Price');
            $table->addCell()->addText('Authors');
            while ($row = $result->fetch_assoc()) {
                $table->addRow();
                $table->addCell()->addText($row['BookId']);
                $table->addCell()->addText($row['Title']);
                $table->addCell()->addText($row['Category']);
                $table->addCell()->addText($row['ISBN']);
                $table->addCell()->addText($row['Publisher']);
                $table->addCell()->addText($row['Year']);
                $table->addCell()->addText($row['Price']);
                $table->addCell()->addText($row['Authors']);
            }
            $section->addText('Total Books: ' . $totalBooks);
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $fileName . '.docx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;

        case 'xlsx':
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Record ID');
            $sheet->setCellValue('B1', 'Membership No');
            $sheet->setCellValue('C1', 'Name');
            $sheet->setCellValue('D1', 'Book ID');
            $sheet->setCellValue('E1', 'Title');
            $sheet->setCellValue('F1', 'Date of Issue');
            $sheet->setCellValue('G1', 'Due Date');
            $sheet->setCellValue('H1', 'Date of Return');
            $sheet->setCellValue('I1', 'Paid Dues');
            $sheet->setCellValue('J1', 'Renewals');
            $sheet->setCellValue('K1', 'Bill Status');

            $rowNumber = 2;
            while ($row = $result->fetch_assoc()) {
                $sheet->setCellValue('A' . $rowNumber, $row['record_id']);
                $sheet->setCellValue('B' . $rowNumber, $row['MembershipNo']);
                $sheet->setCellValue('C' . $rowNumber, $row['Name']);
                $sheet->setCellValue('D' . $rowNumber, $row['BookId']);
                $sheet->setCellValue('E' . $rowNumber, $row['Title']);
                $sheet->setCellValue('F' . $rowNumber, $row['Date_of_Issue']);
                $sheet->setCellValue('G' . $rowNumber, $row['Due_Date']);
                $sheet->setCellValue('H' . $rowNumber, $row['Date_of_Return']);
                $sheet->setCellValue('I' . $rowNumber, $row['trfDues']);
                $sheet->setCellValue('J' . $rowNumber, $row['Renewals']);
                $sheet->setCellValue('K' . $rowNumber, $row['Bill_Status']);
                $rowNumber++;
            }

            $sheet->setCellValue('I' . $rowNumber, 'Total Books:');
            $sheet->setCellValue('J' . $rowNumber, $totalBooks);
            $sheet->setCellValue('I' . ($rowNumber + 1), 'Total Amount:');
            $sheet->setCellValue('J' . ($rowNumber + 1), 'LKR ' . number_format($totalAmount, 2));

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;

        case 'sql':
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment;filename="' . $fileName . '.sql"');
            header('Cache-Control: max-age=0');
            $sqlData = "INSERT INTO record (record_id, MembershipNo, Name, BookId, Title, Date_of_Issue, Due_Date, Date_of_Return, trfDues, Renewals, Bill_Status) VALUES\n";
            while ($row = $result->fetch_assoc()) {
                $sqlData .= "('{$row['record_id']}', '{$row['MembershipNo']}', '{$row['Name']}', '{$row['BookId']}', '{$row['Title']}', '{$row['Date_of_Issue']}', '{$row['Due_Date']}', '{$row['Date_of_Return']}', '{$row['trfDues']}', '{$row['Renewals']}', '{$row['Bill_Status']}'),\n";
            }
            $sqlData = rtrim($sqlData, ",\n") . ";\n";
            $sqlData .= "\n-- Total Books: {$totalBooks}\n";
            $sqlData .= "-- Total Amount: LKR " . number_format($totalAmount, 2) . "\n";
            echo $sqlData;
            exit;

        default:
            echo "Invalid file format selected.";
    }
} else {
    echo "Invalid request method.";
}
?>