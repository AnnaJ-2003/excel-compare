<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

session_start();
$data = $_SESSION['summary'];

$phpWord = new PhpWord();
$section = $phpWord->addSection();

$section->addText("Audit Comparison Summary", ['bold'=>true, 'size'=>16]);
$section->addText("Company: {$data['company']}");
$section->addText("Period: {$data['period']}");
$section->addText("Generated: " . date('d M Y'));

$section->addTextBreak(1);

/* ASSET SUMMARY */
$section->addText("Asset Summary", ['bold'=>true]);

$section->addText("Previous Assets: {$data['assets']['prev']}");
$section->addText("Current Assets: {$data['assets']['curr']}");
$section->addText("Assets Added: {$data['assets']['added']}");
$section->addText("Assets Removed: {$data['assets']['removed']}");

$section->addTextBreak(1);

/* USER SUMMARY */
$section->addText("User Summary", ['bold'=>true]);

$section->addText("Previous Users: {$data['users']['prev']}");
$section->addText("Current Users: {$data['users']['curr']}");
$section->addText("Users Added: {$data['users']['added']}");
$section->addText("Users Removed: {$data['users']['removed']}");

$section->addTextBreak(1);

/* CONCLUSION */
$section->addText("Overall Conclusion", ['bold'=>true]);

$section->addText(
    "The comparison indicates operational changes in both assets and users during the reviewed period."
);

$file = "Audit_Summary_Report.docx";

header("Content-Disposition: attachment; filename=$file");
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save("php://output");
exit;
