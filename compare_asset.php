<?php
session_start();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Asset Comparison</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th {
            background: #333;
            color: white;
        }
        th, td {
            padding: 8px;
            border: 1px solid #999;
        }
        .btn {
            padding: 10px 18px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<h2>Asset List Upload</h2>

<form method="POST" enctype="multipart/form-data">
    <label><b>Previous Asset List</b></label><br>
    <input type="file" name="previous_file" required><br><br>

    <label><b>Current Asset List</b></label><br>
    <input type="file" name="current_file" required><br><br>

    <button class="btn">Upload & Compare</button>
</form>

<hr>

<?php
/* =========================
   PROCESS ONLY AFTER SUBMIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['previous_file'], $_FILES['current_file'])) {
        echo "<p style='color:red'>Files not uploaded</p>";
        exit;
    }

    function ext($name) {
        return strtolower(pathinfo($name, PATHINFO_EXTENSION));
    }

    $prevExt = ext($_FILES['previous_file']['name']);
    $currExt = ext($_FILES['current_file']['name']);

    if (!in_array($prevExt, ['xls','xlsx']) || !in_array($currExt, ['xls','xlsx'])) {
        die("Asset list supports EXCEL files only");
    }

    /* UPLOAD */
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $prevPath = $uploadDir . time() . "_prev." . $prevExt;
    $currPath = $uploadDir . time() . "_curr." . $currExt;

    move_uploaded_file($_FILES['previous_file']['tmp_name'], $prevPath);
    move_uploaded_file($_FILES['current_file']['tmp_name'], $currPath);

    /* PARSE */
    function parseAssetExcel($filePath) {
        $sheet = IOFactory::load($filePath)->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, true);

        $header = array_map('strtolower', $rows[2]);
        $data = [];

        for ($i = 3; $i <= count($rows); $i++) {
            if (empty(array_filter($rows[$i]))) continue;

            $row = array_combine($header, $rows[$i]);
            // Skip section/header rows like "WORKSTATIONS (50)"
if (
    empty($row['device name']) ||
    empty($row['serial number'])
) {
    continue;
}


            $data[] = [
                'key' => strtolower(trim($row['device name']) . '|' . trim($row['serial number'])),
                'device_name' => $row['device name'],
                'type' => $row['type'] ?? '',
                'model' => $row['model'] ?? '',
                'serial' => $row['serial number'] ?? '',
                'os' => $row['os'] ?? '',
                'eol' => $row['end of life'] ?? ''
            ];
        }
        return $data;
    }

    $previousData = parseAssetExcel($prevPath);
    $currentData  = parseAssetExcel($currPath);

    /* COMPARE */
    $prevKeys = array_column($previousData, 'key');
    $currKeys = array_column($currentData, 'key');

    $added   = array_filter($currentData, fn($d) => !in_array($d['key'], $prevKeys));
    $removed = array_filter($previousData, fn($d) => !in_array($d['key'], $currKeys));

    /* SAVE SUMMARY */
    $_SESSION['assets_prev']    = count($previousData);
    $_SESSION['assets_curr']    = count($currentData);
    $_SESSION['assets_added']   = count($added);
    $_SESSION['assets_removed'] = count($removed);

    /* OUTPUT */
    function renderTable($title, $data) {
        echo "<h3>$title (" . count($data) . ")</h3>";
        if (!$data) {
            echo "<p>No records</p>";
            return;
        }
        echo "<table>
                <tr>
                    <th>Device Name</th>
                    <th>Type</th>
                    <th>Model</th>
                    <th>Serial</th>
                    <th>OS</th>
                    <th>EOL</th>
                </tr>";
        foreach ($data as $d) {
            echo "<tr>
                    <td>{$d['device_name']}</td>
                    <td>{$d['type']}</td>
                    <td>{$d['model']}</td>
                    <td>{$d['serial']}</td>
                    <td>{$d['os']}</td>
                    <td>{$d['eol']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    renderTable("Added Assets", $added);
    renderTable("Removed Assets", $removed);

    echo '<br><a href="compare_user.php"><button class="btn">Proceed to User Comparison →</button></a>';
}
?>

</body>
</html>
