<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming $template contains the string with {{variable}} patterns
    $template = file_get_contents('output.zpl');
    
    foreach ($_POST as $variable => $value) {
        // Replace {{variable}} with the selected value
        $template = str_replace("{{" . $variable . "}}", $value, $template);
    }
    
    // Save or use the updated $template as needed
    // For example, save to a new file
    file_put_contents('updated_output.zpl', $template);
}
?>