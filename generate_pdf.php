<?php
require_once 'dompdf/autoload.inc.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['htmlContent']) && isset($_POST['fileLocation']) && isset($_POST['fileName'])) {
    $htmlContent = $_POST['htmlContent'];
    $fileLocation = $_POST['fileLocation'];
    $fileName = $_POST['fileName'];

    // Assurez-vous que le dossier de destination existe, sinon créez-le
    if (!file_exists($fileLocation)) {
        mkdir($fileLocation, 0777, true);
    }

    // Initialize dompdf
    $options = new Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml($htmlContent);

    // Set paper size
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF (first pass to get total pages)
    $dompdf->render();

    // Output PDF as a string
    $output = $dompdf->output();

    // Save the PDF file to the specified location with the specified file name
    file_put_contents($fileLocation . '/' . $fileName . '.pdf', $output);

    // Return a JSON response with success and the base64-encoded PDF content
    echo json_encode(array('success' => true, 'pdfContent' => base64_encode($output)));
    exit;
} else {
    // Return a JSON response with failure
    echo json_encode(array('success' => false));
    exit;
}
?>
