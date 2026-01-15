<?php
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Comparison</title>
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

<h2>User List Upload</h2>

<form method="POST" enctype="multipart/form-data">
    <label><b>Previous User List</b></label><br>
    <input type="file" name="previous_file" required><br><br>

    <label><b>Current User List</b></label><br>
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
        die("User list supports EXCEL files only");
    }

    /* UPLOAD */
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $prevPath = $uploadDir . time() . "_users_prev." . $prevExt;
    $currPath = $uploadDir . time() . "_users_curr." . $currExt;

    move_uploaded_file($_FILES['previous_file']['tmp_name'], $prevPath);
    move_uploaded_file($_FILES['current_file']['tmp_name'], $currPath);

    /* PARSE USERS */
    function parseUserExcel($filePath) {

        $sheet = IOFactory::load($filePath)->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, true);

        $users = [];

        foreach ($rows as $row) {
            foreach ($row as $cell) {

                if (!is_string($cell)) continue;

                $cell = trim($cell);

                if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                    $key = strtolower($cell);
                    $users[$key] = [
                        'key'   => $key,
                        'email' => $cell
                    ];
                }
            }
        }
        return array_values($users);
    }

    $previousUsers = parseUserExcel($prevPath);
    $currentUsers  = parseUserExcel($currPath);

    /* COMPARE */
    $prevKeys = array_column($previousUsers, 'key');
    $currKeys = array_column($currentUsers, 'key');

    $added   = array_filter($currentUsers, fn($u) => !in_array($u['key'], $prevKeys));
    $removed = array_filter($previousUsers, fn($u) => !in_array($u['key'], $currKeys));

    /* SAVE SUMMARY */
    $_SESSION['users_prev']    = count($previousUsers);
    $_SESSION['users_curr']    = count($currentUsers);
    $_SESSION['users_added']   = count($added);
    $_SESSION['users_removed'] = count($removed);

    /* OUTPUT */
    function renderUserTable($title, $data) {
        echo "<h3>$title (" . count($data) . ")</h3>";
        if (!$data) {
            echo "<p>No records</p>";
            return;
        }
        echo "<table>
                <tr>
                    <th>Email Address</th>
                </tr>";
        foreach ($data as $u) {
            echo "<tr>
                    <td>{$u['email']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    renderUserTable("Added Users", $added);
    renderUserTable("Removed Users", $removed);

    echo '<br><a href="summary.php"><button class="btn">View Final Summary →</button></a>';
}
?>

</body>
</html>
