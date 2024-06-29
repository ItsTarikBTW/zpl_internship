<!doctype html>
<html>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class='p-4 flex flex-col justify-center items-center bg-gray-800 text-white h-screen'>
    <?php
    // Csv file path
    $csvFile = 'C:\Users\tarik\OneDrive\Documents\Codes\Laravel\bar-designer\public\db.csv';

    //get line 2 from csv file
    $lines = file($csvFile);
    $line = $lines[1];
    //split line by comma
    $line = explode(';', $line);
    //create array with first element of line

    // get the output from the xmlToZpl.php file
    $templete = file_get_contents('output.zpl');
    // Assuming $templete contains the string with {{variable}} patterns
    $pattern = '/\{\{(\w+)\}\}/'; // Regular expression to match {{variable}} patterns
    preg_match_all($pattern, $templete, $matches);

    $variables = array();
    if (!empty($matches[1])) { // Check if there are any matches
        $variables = $matches[1]; // $matches[1] contains the variable names without the braces
    }

    echo "<h1 class='text-2xl font-bold mb-4'>Select values for the variables</h1>";
    echo "<form action='stdToLabel.php' method='post' class='flex flex-col gap-4 w-96 ring-2 ring-gray-700 p-4 rounded-lg'>";
    foreach ($variables as $variable) {
        //select elements from 0 to 5
        echo "<div class='flex justify-between items-center'>";
        echo "<label for='$variable' class='text-lg truncate'>$variable</label>";
        echo "<select name='$variable' class='p-2 bg-gray-700 text-white rounded-lg text-center'>";
        for ($i = 0; $i < count($line); $i++) {
            echo "<option value='$line[$i]'>$line[$i]</option>"; 
        }
        echo "</select>";
        echo "</div>";
    }
    echo "<button type='submit' class='p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600'>Submit</button>";
    echo "</form>";
    ?>
</body>

</html>