<!doctype html>
<html>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #f5f5f5;
            padding: 8px;
        }
        th {
            background-color: #234b99;
            color: white;
        }
    </style>
</head>

<body class='p-4 flex flex-col justify-center items-center bg-gray-800 text-white h-screen'>
    <?php
    // Csv file path
    $csvFile = 'db.csv';

    // Read the entire CSV file
    $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    echo "<h1 class='text-2xl font-bold mb-4'>Select rows to send</h1>";
    echo "<form action='processSelectedRows.php' method='post' class='w-full max-w-4xl'>";
    echo "<table class='min-w-full leading-normal'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Select</th>";

    // Assuming the first line contains headers
    $headers = explode(';', $lines[0]);
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }

    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($lines as $index => $line) {
        if ($index == 0) continue; // Skip header line
        $columns = explode(';', $line);
        echo "<tr>";
        echo "<td class='flex justify-center items-center gap-2'><input type='checkbox' name='selectedRows[]' value='$index' />$index</td>";
        foreach ($columns as $column) {
            echo "<td>" . htmlspecialchars($column) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "<button type='submit' class='mt-4 p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600'>Submit Selected Rows</button>";
    echo "</form>";
    ?>
</body>

</html>