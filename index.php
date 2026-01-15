<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Comparison System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: white;
            padding: 30px 40px;
            width: 420px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 10px;
        }
        p {
            color: #555;
            font-size: 14px;
            margin-bottom: 25px;
        }
        button {
            padding: 12px 20px;
            font-size: 15px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background: #0056b3;
        }
        .note {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Audit Comparison System</h1>
    <p>
        Upload and compare  lists to identify
        additions and removals, and generate consolidated audit reports.
    </p>

    <form action="compare_asset.php" method="get">
        <button>Start Comparison</button>
    </form>

    <div class="note">
        Step 1: Asset Comparison → Step 2: User Comparison → Step 3: Summary
    </div>
</div>

</body>
</html>
