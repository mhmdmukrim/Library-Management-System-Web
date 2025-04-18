<?php
require('dbconn.php');
require_once('vendor/autoload.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    $membershipNo = isset($_POST['membership_no']) ? $_POST['membership_no'] : '';
    $fileFormat = $_POST['file_format'];

    $query = "SELECT r.record_id, r.MembershipNo, m.Name, r.BookId, b.Title, r.Date_of_Issue, r.Due_Date, r.Date_of_Return, r.trfDues, r.Renewals, r.Bill_Status
              FROM record r
              JOIN user m ON r.MembershipNo = m.MembershipNo
              JOIN book b ON r.BookId = b.BookId
              WHERE r.Date_of_Issue BETWEEN ? AND ?";
    if (!empty($membershipNo)) {
        $query .= " AND r.MembershipNo = ?";
    }

    $stmt = $conn->prepare($query);
    if (!empty($membershipNo)) {
        $stmt->bind_param("sss", $fromDate, $toDate, $membershipNo);
    } else {
        $stmt->bind_param("ss", $fromDate, $toDate);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Calculate totals
    $totalAmount = 0;
    $totalTransactions = $result->num_rows;
    while ($row = $result->fetch_assoc()) {
        $totalAmount += $row['trfDues'];
    }

    $result->data_seek(0); // Reset pointer to the beginning of the result set

    $formattedFromDate = date('Y-m-d', strtotime($fromDate));
    $formattedToDate = date('Y-m-d', strtotime($toDate));
    $fileName = "progress_report_{$formattedFromDate}_to_{$formattedToDate}";

    switch ($fileFormat) {
        case 'pdf':
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Akurana Public Library');
            $pdf->SetTitle('Progress Report');
            $pdf->SetSubject('Progress Report from ' . $fromDate . ' to ' . $toDate);
            $pdf->SetKeywords('Progress Report, Library');

            $pdf->SetHeaderData('', 0, 'Progress Report', 'From: ' . $fromDate . ' To: ' . $toDate);
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 12);

            $html = '<h1>Progress Report</h1>';
            $html .= '<table border="1" cellspacing="3" cellpadding="4">
                        <thead>
                            <tr>
                                <th>Record ID</th>
                                <th>Membership No</th>
                                <th>Name</th>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th>Date of Issue</th>
                                <th>Due Date</th>
                                <th>Date of Return</th>
                                <th>Paid Dues</th>
                                <th>Renewals</th>
                                <th>Bill Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                            <td>' . $row['record_id'] . '</td>
                            <td>' . $row['MembershipNo'] . '</td>
                            <td>' . $row['Name'] . '</td>
                            <td>' . $row['BookId'] . '</td>
                            <td>' . $row['Title'] . '</td>
                            <td>' . $row['Date_of_Issue'] . '</td>
                            <td>' . $row['Due_Date'] . '</td>
                            <td>' . $row['Date_of_Return'] . '</td>
                            <td>' . $row['trfDues'] . '</td>
                            <td>' . $row['Renewals'] . '</td>
                            <td>' . $row['Bill_Status'] . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
            $html .= '<h3>Total Transactions: ' . $totalTransactions . '</h3>';
            $html .= '<h3>Total Amount: LKR ' . number_format($totalAmount, 2) . '</h3>';
            $pdf->writeHTML($html);
            $pdf->Output($fileName . '.pdf', 'D');
            exit;

        case 'doc':
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $section->addText('Progress Report from ' . $fromDate . ' to ' . $toDate);
            $table = $section->addTable();
            $table->addRow();
            $table->addCell()->addText('Record ID');
            $table->addCell()->addText('Membership No');
            $table->addCell()->addText('Name');
            $table->addCell()->addText('Book ID');
            $table->addCell()->addText('Title');
            $table->addCell()->addText('Date of Issue');
            $table->addCell()->addText('Due Date');
            $table->addCell()->addText('Date of Return');
            $table->addCell()->addText('Paid Dues');
            $table->addCell()->addText('Renewals');
            $table->addCell()->addText('Bill Status');
            while ($row = $result->fetch_assoc()) {
                $table->addRow();
                $table->addCell()->addText($row['record_id']);
                $table->addCell()->addText($row['MembershipNo']);
                $table->addCell()->addText($row['Name']);
                $table->addCell()->addText($row['BookId']);
                $table->addCell()->addText($row['Title']);
                $table->addCell()->addText($row['Date_of_Issue']);
                $table->addCell()->addText($row['Due_Date']);
                $table->addCell()->addText($row['Date_of_Return']);
                $table->addCell()->addText($row['trfDues']);
                $table->addCell()->addText($row['Renewals']);
                $table->addCell()->addText($row['Bill_Status']);
            }
            $section->addText('Total Transactions: ' . $totalTransactions);
            $section->addText('Total Amount: LKR ' . number_format($totalAmount, 2));
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

            $sheet->setCellValue('I' . $rowNumber, 'Total Transactions:');
            $sheet->setCellValue('J' . $rowNumber, $totalTransactions);
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
            $sqlData .= "\n-- Total Transactions: {$totalTransactions}\n";
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
