<?php
require_once 'dompdf/autoload.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['htmlContent'])) {
    $htmlContent = $_POST['htmlContent'];

    // Initialize dompdf
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($htmlContent);

    // Set paper size
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF (first pass to get total pages)
    $dompdf->render();

    // Output PDF as a string
    $output = $dompdf->output();

    // Save the PDF on the server
    $pdfFilePath = 'C:\Users\Malala\Desktop\Test\generated_pdf.pdf';
    file_put_contents($pdfFilePath, $output);

    // Return the file path to the client
    echo $pdfFilePath;
    exit;
}
?>





