<?php
require_once('tcpdf/tcpdf.php');
include 'config.php';

// Fetch only the latest resume
$result = $conn->query("SELECT * FROM resumes ORDER BY created_at DESC LIMIT 1");

if (!$result || $result->num_rows == 0) {
    die("No resume found.");
}

$resume = $result->fetch_assoc();

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Document meta
$pdf->SetCreator('Resume Manager');
$pdf->SetAuthor($resume['name']);
$pdf->SetTitle('Resume - ' . $resume['name']);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

// Use 'courier' font to avoid missing font files error
$pdf->SetFont('courier', '', 10);

// Colors
$blue = [37, 99, 235];     // #2563eb
$darkBlue = [30, 64, 175]; // #1e40af
$grayText = [85, 85, 85];  // #555
$blackText = [51, 51, 51]; // #333

// Page width and margins
$pageWidth = $pdf->getPageWidth();
$leftMargin = $pdf->getMargins()['left'];
$rightMargin = $pdf->getMargins()['right'];
$photoDiameter = 35;
$spacing = 4;

// Add profile photo on right side with circle border effect
if (!empty($resume['photo']) && file_exists($resume['photo'])) {
    $xPhoto = $pageWidth - $rightMargin - $photoDiameter;
    $yPhoto = 20;

    $pdf->SetDrawColor(...$blue);
    $pdf->SetLineWidth(0.8);
    $pdf->Circle($xPhoto + $photoDiameter / 2, $yPhoto + $photoDiameter / 2, $photoDiameter / 2, 0, 360, 'D');

    $pdf->Image($resume['photo'], $xPhoto, $yPhoto, $photoDiameter, $photoDiameter, '', '', '', true, 300, '', false, false, 0, false, false, true);

    $pdf->SetXY($leftMargin, $yPhoto + 5);
    $pdf->SetFont('courier', 'B', 16);
    $pdf->SetTextColor(...$darkBlue);
    $pdf->Cell(0, 8, $resume['name'], 0, 1, 'L');

    $pdf->SetFont('courier', '', 9);
    $pdf->SetTextColor(...$grayText);
    $contactInfo =
        "Location: " . $resume['address'] . "\n" .
        "Phone: " . $resume['mobile'] . "\n" .
        "Email: " . $resume['email'];
    $pdf->MultiCell(0, 5, $contactInfo, 0, 'L', 0, 1);

    $pdf->SetXY($leftMargin, $yPhoto + $photoDiameter + 10);
} else {
    $pdf->SetXY($leftMargin, 25);
    $pdf->SetFont('courier', 'B', 16);
    $pdf->SetTextColor(...$darkBlue);
    $pdf->Cell(0, 8, $resume['name'], 0, 1, 'L');

    $pdf->SetFont('courier', '', 9);
    $pdf->SetTextColor(...$grayText);
    $contactInfo =
        "Location: " . $resume['address'] . "\n" .
        "Phone: " . $resume['mobile'] . "\n" .
        "Email: " . $resume['email'];
    $pdf->MultiCell(0, 5, $contactInfo, 0, 'L', 0, 1);

    $pdf->Ln(8);
}

// Helper function
function addSection($pdf, $title, $content, $blueColor, $blackColor) {
    if (trim($content) === '') return;

    $pdf->SetFont('courier', 'B', 12);
    $pdf->SetTextColor(...$blueColor);
    $pdf->Cell(0, 6, $title, 0, 1, 'L');

    $startX = $pdf->GetX();
    $currentY = $pdf->GetY();
    $pageWidth = $pdf->getPageWidth();
    $rightMargin = $pdf->getMargins()['right'];
    $pdf->SetDrawColor(...$blueColor);
    $pdf->SetLineWidth(0.8);
    $pdf->Line($startX, $currentY, $pageWidth - $rightMargin, $currentY);

    $pdf->Ln(4);

    $pdf->SetFont('courier', '', 9);
    $pdf->SetTextColor(...$blackColor);
    $pdf->MultiCell(0, 5, $content, 0, 'L', 0, 1);

    $pdf->Ln(6);
}

// Add resume sections
addSection($pdf, 'Profile', $resume['profile'], $blue, $blackText);
addSection($pdf, 'Skills', $resume['skills'], $blue, $blackText);
addSection($pdf, 'Education', $resume['education'], $blue, $blackText);
addSection($pdf, 'Experience', $resume['experience'], $blue, $blackText);
addSection($pdf, 'Projects', $resume['projects'], $blue, $blackText);

// Output PDF
$fileName = 'Resume_' . preg_replace('/[^A-Za-z0-9]/', '_', $resume['name']) . '.pdf';
$pdf->Output($fileName, 'D');
exit;
