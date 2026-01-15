<?php
session_start();

/* 🔐 Safety check */
if (!isset($_SESSION['summary'])) {
    die("No summary data available. Please run comparison first.");
}

$data = $_SESSION['summary'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comparison Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 60%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
        }
        th {
            background: #333;
            color: #fff;
        }
        .btn {
            padding: 10px 16px;
            margin-right: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h2>Audit Comparison Summary</h2>

<p>
<b>Company:</b> <?= htmlspecialchars($data['company']) ?><br>
<b>Period:</b> <?= htmlspecialchars($data['period']) ?><br>
<b>Generated On:</b> <?= date('d M Y') ?>
</p>

<!-- ASSET SUMMARY -->
<h3>Asset Summary</h3>
<table>
    <tr>
        <th>Previous</th>
        <th>Current</th>
        <th>Added</th>
        <th>Removed</th>
    </tr>
    <tr>
        <td><?= $data['assets']['prev'] ?></td>
        <td><?= $data['assets']['curr'] ?></td>
        <td><?= $data['assets']['added'] ?></td>
        <td><?= $data['assets']['removed'] ?></td>
    </tr>
</table>

<!-- USER SUMMARY -->
<h3>User Summary</h3>
<table>
    <tr>
        <th>Previous</th>
        <th>Current</th>
        <th>Added</th>
        <th>Removed</th>
    </tr>
    <tr>
        <td><?= $data['users']['prev'] ?></td>
        <td><?= $data['users']['curr'] ?></td>
        <td><?= $data['users']['added'] ?></td>
        <td><?= $data['users']['removed'] ?></td>
    </tr>
</table>

<!-- DOWNLOAD BUTTONS -->
<a href="export_summary_word.php">
    <button class="btn">Download Word Summary</button>
</a>

<a href="export_summary_pdf.php">
    <button class="btn">Download PDF Summary</button>
</a>

</body>
</html>
