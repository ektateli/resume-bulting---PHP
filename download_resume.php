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

// Use a professional font: 'helvetica' or 'times' (built-in in TCPDF)
$fontName = 'helvetica'; // alternative: 'times'
$pdf->SetFont($fontName, '', 10);

// Colors
$blue = [37, 99, 235];     // #2563eb
$darkBlue = [30, 64, 175]; // #1e40af
$grayText = [85, 85, 85];  // #555
$blackText = [51, 51, 51]; // #333

// Get page width and margins for positioning
$pageWidth = $pdf->getPageWidth();
$leftMargin = $pdf->getMargins()['left'];
$rightMargin = $pdf->getMargins()['right'];
$photoDiameter = 35;  // slightly smaller photo to save space
$spacing = 4;         // spacing between elements

// Add profile photo on right side with circle border effect
if (!empty($resume['photo']) && file_exists($resume['photo'])) {
    $xPhoto = $pageWidth - $rightMargin - $photoDiameter; // right side
    $yPhoto = 20;

    // Draw circle border for photo
    $pdf->SetDrawColor(...$blue);
    $pdf->SetLineWidth(0.8);
    $pdf->Circle($xPhoto + $photoDiameter / 2, $yPhoto + $photoDiameter / 2, $photoDiameter / 2, 0, 360, 'D');

    // Place image inside circle
    $pdf->Image($resume['photo'], $xPhoto, $yPhoto, $photoDiameter, $photoDiameter, '', '', '', true, 300, '', false, false, 0, false, false, true);

    // Text on left side (starting near left margin)
    $pdf->SetXY($leftMargin, $yPhoto + 5);

    // Name (bigger, bold)
    $pdf->SetFont($fontName, 'B', 16);
    $pdf->SetTextColor(...$darkBlue);
    $pdf->Cell(0, 8, $resume['name'], 0, 1, 'L');

    // Contact info (smaller)
    $pdf->SetFont($fontName, '', 9);
    $pdf->SetTextColor(...$grayText);
    $contactInfo = 
        "Location: " . $resume['address'] . "\n" .
        "Phone: " . $resume['mobile'] . "\n" .
        "Email: " . $resume['email'];
    $pdf->MultiCell(0, 5, $contactInfo, 0, 'L', 0, 1);

    // Move cursor below photo for next content
    $pdf->SetXY($leftMargin, $yPhoto + $photoDiameter + 10);
} else {
    // No photo, write from left margin
    $pdf->SetXY($leftMargin, 25);

    // Name
    $pdf->SetFont($fontName, 'B', 16);
    $pdf->SetTextColor(...$darkBlue);
    $pdf->Cell(0, 8, $resume['name'], 0, 1, 'L');

    // Contact info
    $pdf->SetFont($fontName, '', 9);
    $pdf->SetTextColor(...$grayText);
    $contactInfo = 
        "Location: " . $resume['address'] . "\n" .
        "Phone: " . $resume['mobile'] . "\n" .
        "Email: " . $resume['email'];
    $pdf->MultiCell(0, 5, $contactInfo, 0, 'L', 0, 1);

    $pdf->Ln(8);
}

// Helper function to add section with title and content, adjusted for compactness
function addSection($pdf, $title, $content, $blueColor, $blackColor, $fontName) {
    if (trim($content) === '') return;

    // Section title - smaller than before
    $pdf->SetFont($fontName, 'B', 12);
    $pdf->SetTextColor(...$blueColor);
    $pdf->Cell(0, 6, $title, 0, 1, 'L');

    // Underline
    $startX = $pdf->GetX();
    $currentY = $pdf->GetY();
    $pageWidth = $pdf->getPageWidth();
    $rightMargin = $pdf->getMargins()['right'];
    $pdf->SetDrawColor(...$blueColor);
    $pdf->SetLineWidth(0.8);
    $pdf->Line($startX, $currentY, $pageWidth - $rightMargin, $currentY);

    $pdf->Ln(4);

    // Section content, smaller font, less line spacing for compactness
    $pdf->SetFont($fontName, '', 9);
    $pdf->SetTextColor(...$blackColor);
    $pdf->MultiCell(0, 5, $content, 0, 'L', 0, 1);

    $pdf->Ln(6);
}

// Add resume sections with compact style to fit on one page
addSection($pdf, 'Profile', $resume['profile'], $blue, $blackText, $fontName);
addSection($pdf, 'Skills', $resume['skills'], $blue, $blackText, $fontName);
addSection($pdf, 'Education', $resume['education'], $blue, $blackText, $fontName);
addSection($pdf, 'Experience', $resume['experience'], $blue, $blackText, $fontName);
addSection($pdf, 'Projects', $resume['projects'], $blue, $blackText, $fontName);

// Output PDF for download
$fileName = 'Resume_' . preg_replace('/[^A-Za-z0-9]/', '_', $resume['name']) . '.pdf';
$pdf->Output($fileName, 'D');
exit;
