
<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

session_start();

$addedDevices   = $_SESSION['addedDevices'] ?? [];
$removedDevices = $_SESSION['removedDevices'] ?? [];

$phpWord = new PhpWord();
$section = $phpWord->addSection();

/* =========================
   TITLE
========================= */
$section->addText(
    'Asset Comparison Summary Report',
    ['bold' => true, 'size' => 16],
    ['alignment' => 'center']
);

$section->addTextBreak(1);

/* =========================
   SUMMARY
========================= */
$section->addText("Summary", ['bold' => true, 'size' => 13]);

$section->addText("Total Added Devices: " . count($addedDevices));
$section->addText("Total Removed Devices: " . count($removedDevices));

$section->addTextBreak(1);

/* =========================
   ADDED DEVICES
========================= */
$section->addText("Added Devices", ['bold' => true]);

if (!$addedDevices) {
    $section->addText("No devices were added.");
} else {
    foreach ($addedDevices as $d) {
        $section->addText(
            "- {$d['device_name']} ({$d['model']})"
        );
    }
}

$section->addTextBreak(1);

/* =========================
   REMOVED DEVICES
========================= */
$section->addText("Removed Devices", ['bold' => true]);

if (!$removedDevices) {
    $section->addText("No devices were removed.");
} else {
    foreach ($removedDevices as $d) {
        $section->addText(
            "- {$d['device_name']} ({$d['model']})"
        );
    }
}

/* =========================
   DOWNLOAD
========================= */
$filename = "asset_comparison_summary.docx";

header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Cache-Control: must-revalidate');
header('Expires: 0');

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save("php://output");
exit;
