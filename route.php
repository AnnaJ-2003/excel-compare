<?php
if (!isset($_POST['report_type'])) {
    die("Report type missing");
}

$reportType = $_POST['report_type'];

if ($reportType === 'asset') {
    header("Location: compare_asset.php");
    exit;
}

if ($reportType === 'user') {
    header("Location: compare_user.php");
    exit;
}

die("Invalid report type");
