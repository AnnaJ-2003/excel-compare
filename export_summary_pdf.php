<?php
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

session_start();

/* 🔒 SAFETY: disable errors from being printed */
ini_set('display_errors', 0);
error_reporting(0);

/* ✅ Check session data */
if (!isset($_SESSION['summary'])) {
    die('Summary data missing. Run comparison first.');
}

$d = $_SESSION['summary'];

/* ✅ HTML must be PURE HTML (no PHP echo later) */
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
h2 { text-align: center; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #000; padding: 6px; text-align: left; }
th { background: #eee; }
</style>
</head>
<body>

<h2>Audit Comparison Summary</h2>

<p><b>Company:</b> '.$d['company'].'<br>
<b>Period:</b> '.$d['period'].'<br>
<b>Generated:</b> '.date('d M Y').'</p>

<h3>Asset Summary</h3>
<table>
<tr><th>Previous</th><th>Current</th><th>Added</th><th>Removed</th></tr>
<tr>
<td>'.$d['assets']['prev'].'</td>
<td>'.$d['assets']['curr'].'</td>
<td>'.$d['assets']['added'].'</td>
<td>'.$d['assets']['removed'].'</td>
</tr>
</table>

<h3>User Summary</h3>
<table>
<tr><th>Previous</th><th>Current</th><th>Added</th><th>Removed</th></tr>
<tr>
<td>'.$d['users']['prev'].'</td>
<td>'.$d['users']['curr'].'</td>
<td>'.$d['users']['added'].'</td>
<td>'.$d['users']['removed'].'</td>
</tr>
</table>

<h3>Overall Conclusion</h3>
<p>
The comparison shows changes in assets and users during the selected period,
indicating operational updates within the organization.
</p>

</body>
</html>
';

/* ✅ Create PDF */
$pdf = new Dompdf([
    'isRemoteEnabled' => true
]);

$pdf->loadHtml($html);
$pdf->setPaper('A4', 'portrait');
$pdf->render();

/* ✅ IMPORTANT: clean output buffer */
ob_end_clean();

/* ✅ Stream PDF */
$pdf->stream("Audit_Summary_Report.pdf", [
    "Attachment" => true
]);
exit;
