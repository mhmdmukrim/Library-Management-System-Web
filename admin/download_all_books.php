<?php
require ('dbconn.php');
require_once ('vendor/autoload.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileFormat = $_POST['file_format'];

    $query = "SELECT b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price, GROUP_CONCAT(a.Author SEPARATOR ', ') as Authors
    FROM book b
    LEFT JOIN author a ON b.BookId = a.BookId
    GROUP BY b.BookId, b.Title, b.Category, b.ISBN, b.Publisher, b.Year, b.Price";

    $result = $conn->query($query);

    $fileName = "all_books_report";

    switch ($fileFormat) {
        case 'pdf':
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Akurana Public Library');
            $pdf->SetTitle('All Books Report');
            $pdf->SetSubject('All Books Report');
            $pdf->SetKeywords('All Books, Library');

            $pdf->SetHeaderData('', 0, 'All Books Report', '');
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

            $html = '<h1>All Books Report</h1>';
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
                            <td>' . htmlspecialchars($row['BookId']) . '</td>
                            <td>' . htmlspecialchars($row['Title']) . '</td>
                            <td>' . htmlspecialchars($row['Category']) . '</td>
                            <td>' . htmlspecialchars($row['ISBN']) . '</td>
                            <td>' . htmlspecialchars($row['Publisher']) . '</td>
                            <td>' . htmlspecialchars($row['Year']) . '</td>
                            <td>' . htmlspecialchars($row['Price']) . '</td>
                            <td>' . htmlspecialchars($row['Authors']) . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
            $pdf->writeHTML($html);
            $pdf->Output($fileName . '.pdf', 'D');
            exit;

        case 'doc':
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $section->addText('All Books Report');
            $table = $section->addTable();
            $table->addRow();
            $table->addCell()->addText('Book ID');
            $table->addCell()->addText('Title');
            $table->addCell()->addText('Category');
            $table->addCell()->addText('ISBN');
            $table->addCell()->addText('Publisher');
            $table->addCell()->addText('Year');
            $table->addCell()->addText('Price (LKR)');
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
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $fileName . '.docx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;

        case 'xlsx':
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Book ID');
            $sheet->setCellValue('B1', 'Title');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'ISBN');
            $sheet->setCellValue('E1', 'Publisher');
            $sheet->setCellValue('F1', 'Year');
            $sheet->setCellValue('G1', 'Price (LKR)');
            $sheet->setCellValue('H1', 'Authors');

            $rowNumber = 2;
            while ($row = $result->fetch_assoc()) {
                $sheet->setCellValue('A' . $rowNumber, $row['BookId']);
                $sheet->setCellValue('B' . $rowNumber, $row['Title']);
                $sheet->setCellValue('C' . $rowNumber, $row['Category']);
                $sheet->setCellValue('D' . $rowNumber, $row['ISBN']);
                $sheet->setCellValue('E' . $rowNumber, $row['Publisher']);
                $sheet->setCellValue('F' . $rowNumber, $row['Year']);
                $sheet->setCellValue('G' . $rowNumber, $row['Price']);
                $sheet->setCellValue('H' . $rowNumber, $row['Authors']);
                $rowNumber++;
            }

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
            $sqlData = "INSERT INTO book (BookId, Title, Category, ISBN, Publisher, Year, Price, Authors) VALUES\n";
            while ($row = $result->fetch_assoc()) {
                $sqlData .= "('" . $row['BookId'] . "', '" . $row['Title'] . "',  '" . $row['Category'] . "', '" . $row['ISBN'] . "', '" . $row['Publisher'] . "', '" . $row['Year'] . "',  '" . $row['Price'] . "', '" . $row['Authors'] . "'),\n";
            }
            $sqlData = rtrim($sqlData, ",\n") . ";\n";
            echo $sqlData;
            exit;

        default:
            echo "Invalid file format selected.";
    }
} else {
    echo "Invalid request method.";
}
?>