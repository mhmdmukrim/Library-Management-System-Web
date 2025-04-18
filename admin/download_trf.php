<?php
require ('dbconn.php');
require_once ('vendor/autoload.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    $membershipNo = isset($_POST['membership_no']) ? $_POST['membership_no'] : '';
    $fileFormat = $_POST['file_format']; // Get the file format from the POST request

    // Prepare and execute the query
    $query = "SELECT r.trf_date, SUM(r.trfDues) as total_trfDues
              FROM record r
              WHERE r.trf_date BETWEEN ? AND ?";
    if (!empty($membershipNo)) {
        $query .= " AND r.MembershipNo = ?";
    }
    $query .= " GROUP BY r.trf_date";

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
        $totalAmount += $row['total_trfDues'];
    }

    $result->data_seek(0); // Reset pointer to the beginning of the result set

    $formattedFromDate = date('Y-m-d', strtotime($fromDate));
    $formattedToDate = date('Y-m-d', strtotime($toDate));
    $fileName = "transfer_report_{$formattedFromDate}_to_{$formattedToDate}";

    switch ($fileFormat) {
        case 'pdf':
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Akurana Public Library');
            $pdf->SetTitle('Transfer Report');
            $pdf->SetSubject('Transfer Report from ' . $fromDate . ' to ' . $toDate);
            $pdf->SetKeywords('Transfer Report, Library');

            $pdf->SetHeaderData('', 0, 'Transfer Report', 'From: ' . $fromDate . ' To: ' . $toDate);
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

            $html = '<h1>Transfer Report</h1>';
            $html .= '<table border="1" cellspacing="3" cellpadding="4">
                        <thead>
                            <tr>
                                <th>Transfer Date</th>
                                <th>Transferred Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                            <td>' . $row['trf_date'] . '</td>
                            <td>' . $row['total_trfDues'] . '</td>
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
            $section->addText('Transfer Report from ' . $fromDate . ' to ' . $toDate);
            $table = $section->addTable();
            $table->addRow();
            $table->addCell()->addText('Transfer Date');
            $table->addCell()->addText('Transferred Total Amount');
            while ($row = $result->fetch_assoc()) {
                $table->addRow();
                $table->addCell()->addText($row['trf_date']);
                $table->addCell()->addText($row['total_trfDues']);
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
            $sheet->setCellValue('A1', 'Transfer Date');
            $sheet->setCellValue('B1', 'Transferred Total Amount');

            $rowNumber = 2;
            while ($row = $result->fetch_assoc()) {
                $sheet->setCellValue('A' . $rowNumber, $row['trf_date']);
                $sheet->setCellValue('B' . $rowNumber, $row['total_trfDues']);
                $rowNumber++;
            }

            $sheet->setCellValue('A' . $rowNumber, 'Total Transactions:');
            $sheet->setCellValue('B' . $rowNumber, $totalTransactions);
            $sheet->setCellValue('A' . ($rowNumber + 1), 'Total Amount:');
            $sheet->setCellValue('B' . ($rowNumber + 1), 'LKR ' . number_format($totalAmount, 2));

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
            $sqlData = "INSERT INTO record (trf_date, total_trfDues) VALUES\n";
            while ($row = $result->fetch_assoc()) {
                $sqlData .= "('{$row['trf_date']}', '{$row['total_trfDues']}'),\n";
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
