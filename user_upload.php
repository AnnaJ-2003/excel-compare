<?php
session_start();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

function readUsersFile($filePath) {
    $sheet = IOFactory::load($filePath)->getActiveSheet();
    $rows  = $sheet->toArray(null, true, true, true);
    $users = [];
    foreach ($rows as $row) {
        foreach ($row as $cell) {
            if (!is_string($cell)) continue;
            $cell = trim($cell);
            if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                $key = strtolower($cell);
                $users[$key] = ['key'=>$key, 'email'=>$cell];
            }
        }
    }
    return array_values($users);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_FILES['previous_file']['tmp_name']) || empty($_FILES['current_file']['tmp_name'])) {
        die("Please upload both user files.");
    }

    $previousUsers = readUsersFile($_FILES['previous_file']['tmp_name']);
    $currentUsers  = readUsersFile($_FILES['current_file']['tmp_name']);

    $prevKeys = array_column($previousUsers, 'key');
    $currKeys = array_column($currentUsers, 'key');

    $added   = array_filter($currentUsers, fn($u) => !in_array($u['key'], $prevKeys));
    $removed = array_filter($previousUsers, fn($u) => !in_array($u['key'], $currKeys));

    // Save user counts in session
    $_SESSION['users_prev']    = count($previousUsers);
    $_SESSION['users_curr']    = count($currentUsers);
    $_SESSION['users_added']   = count($added);
    $_SESSION['users_removed'] = count($removed);

    // Build final summary
    $_SESSION['summary'] = [
        'company' => 'ABC Technologies',
        'period'  => 'Previous vs Current',
        'assets' => [
            'prev' => $_SESSION['assets_prev'] ?? 0,
            'curr' => $_SESSION['assets_curr'] ?? 0,
            'added' => $_SESSION['assets_added'] ?? 0,
            'removed' => $_SESSION['assets_removed'] ?? 0
        ],
        'users' => [
            'prev' => $_SESSION['users_prev'],
            'curr' => $_SESSION['users_curr'],
            'added' => $_SESSION['users_added'],
            'removed' => $_SESSION['users_removed']
        ]
    ];

    // Redirect to summary page
    header("Location: summary.php");
    exit;
}

?>

<!-- User Upload Form -->
<h2>Upload User Lists</h2>
<form action="" method="POST" enctype="multipart/form-data">
    Previous User List: <input type="file" name="previous_file" required><br><br>
    Current User List: <input type="file" name="current_file" required><br><br>
    <button type="submit">Compare Users</button>
</form>
