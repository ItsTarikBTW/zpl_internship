<?php
function decodeEncodedPatterns($text)
{
    // Define an associative array of encoded patterns and their replacements
    $patterns = [
        '_x002F_' => '/', // Slash
        '_x0020_' => ' ', // Space
        '_x0040_' => '@', // At symbol
        '_x0026_' => '&', // Ampersand
        '_x0023_' => '#', // Hash
        '_x0025_' => '%', // Percent
        '_x0028_' => '(', // Opening parenthesis
        '_x0029_' => ')', // Closing parenthesis
        '_x002D_' => '-', // Hyphen
        '_x002B_' => '+', // Plus
        '_x002E_' => '.', // Period
        '_x002C_' => ',', // Comma
        '_x0021_' => '!', // Exclamation mark
        '_x003F_' => '?', // Question mark
        '_x0022_' => '"', // Double quote
        '_x0027_' => "'", // Single quote
        '_x003A_' => ':', // Colon
        '_x003B_' => ';', // Semicolon
        '_x003D_' => '=', // Equal sign
        '_x003C_' => '<', // Less than
        '_x003E_' => '>', // Greater than
        '_x005B_' => '[', // Opening square bracket
        '_x005D_' => ']', // Closing square bracket
        '_x007B_' => '{', // Opening curly brace
        '_x007D_' => '}', // Closing curly brace
        '_x005C_' => '\\', // Backslash
        '_x007C_' => '|', // Pipe
        '_x002A_' => '*', // Asterisk
        '_x002F_' => '/', // Forward slash
        '_x00A3_' => '£', // Pound sterling
        '_x20AC_' => '€', // Euro
        '_x00A5_' => '¥', // Yen
        '_x00A7_' => '§', // Section
        '_x00A9_' => '©', // Copyright
        '_x00AE_' => '®', // Registered trademark
        '_x2122_' => '™', // Trademark
        '_x0031_' => '1', // 1
        '_x0032_' => '2', // 2
        '_x0033_' => '3', // 3
        '_x0034_' => '4', // 4
        '_x0035_' => '5', // 5
        '_x0036_' => '6', // 6
        '_x0037_' => '7', // 7
        '_x0038_' => '8', // 8
        '_x0039_' => '9', // 9
        '_x0030_' => '0', // 0
    ];

    // Iterate over the array and replace each pattern in the text
    foreach ($patterns as $encoded => $decoded) {
        $text = str_replace($encoded, $decoded, $text);
    }

    return $text;
}

$xmlString = file_get_contents('C:\Users\tarik\OneDrive\Documents\Codes\Laravel\bar-designer\public\testcase.xml'); // Load the XML from a file
$xml = new SimpleXMLElement($xmlString);

// Assuming the units in the XML are in inches and the printer resolution is 203 DPI.
$dpi = 203; // Dots per inch for 203 DPI printers. Adjust this value for printers with different resolutions.
$cmToInch = 1 / 2.54; // Conversion factor from cm to inches
$index = 0;
$zpl = "^XA\n\n"; // Start of ZPL script with a newline for readability
foreach ($xml->Items->children() as $item) {
    // Convert dimensions from inches to dots
    $x = (int)(floatval($item['X']) * $cmToInch * $dpi);
    $y = (int)(floatval($item['Y']) * $cmToInch * $dpi);
    $width = (int)(floatval($item['Width']) * $cmToInch * $dpi);
    $height = (int)(floatval($item['Height']) * $cmToInch * $dpi);
    $strokeThickness = (int)(floatval($item['StrokeThickness']) * $cmToInch * $dpi); // Assuming StrokeThickness is also in cm

    switch ($item->getName()) {
        case 'RectangleShapeItem':
            // Use graphic boxes (^GB) for rectangles, converting dimensions to dots.
            $zpl .= "^FO$x,$y^GB$width,$height,$strokeThickness^FS\n";
            break;
        case 'TextItem':
            // Extract font properties
            $fontDetails = explode(',', $item['Font']);
            $fontName = $fontDetails[0]; // Simplified handling, real implementation may vary
            $fontSize = $fontDetails[1];
            $isBold = $fontDetails[2] === 'True';
            $isItalic = $fontDetails[3] === 'True'; // Italic not directly supported in ZPL, shown for completeness

            // Map to ZPL font command (simplified, focusing on font size)
            // Assuming ^A0N is the default font, and size is directly applicable
            $zplFontSize = $fontSize; // Simplify, real mapping may need conversion

            // ForeColor handling (simplified, assuming black text)
            $foreColor = $item['ForeColor'] === 'White' ? '^FR' : ''; // ^FR reverses images, can simulate white text on black

            // Use ^FO (Field Origin), ^A (Font), ^FD (Field Data) for text, with positions in dots.
            $text = ($item['Name'] == "") ? decodeEncodedPatterns($item['Text']) : "{{" . $item['Name'] . "}}";
            $zpl .= "^FO$x,$y^A0N,$zplFontSize,$zplFontSize$foreColor^FD$text^FS\n";
            break;
        case 'BarcodeItem':
            if ($item['Symbology'] == 'QRCode') {
                // QR Code specific ZPL
                $magnificationFactor = isset($item['MagnificationFactor']) ? (int)$item['MagnificationFactor'] : 2; // Default magnification factor
                $text = ($item['Name'] == "") ? decodeEncodedPatterns($item['Code']) : "{{" . $item['Name'] . "}}";
                $errorCorrection = isset($item['ErrorCorrection']) ? $item['ErrorCorrection'] : 'Q'; // Default error correction
                $zpl .= "^FO$x,$y^BQN,2,$magnificationFactor^FDHA,$text^FS\n";
            } else {
                $text = ($item['Name'] == "") ? decodeEncodedPatterns($item['Code']) : "{{" . $item['Name'] . "}}";
                // Existing barcode logic
                $rotation = 'N'; // Default rotation
                $rotationAngle = (int)$item['RotationAngle'];
                switch ($rotationAngle) {
                    case 90:
                        $rotation = 'R';
                        break;
                    case 180:
                        $rotation = 'I';
                        break;
                    case 270:
                        $rotation = 'B';
                        break;
                }
                $zpl .= "^FO$x,$y^BY2^BC$rotation,$height,Y,N,N,^FD{$text}^FS\n";
            }
            break;
        case 'ImageItem':
            // Read the image file
            $imagePath = (string)$item['SourceData'];
            if (file_exists($imagePath)) {
                // Load and convert image to binary string in ZPL format
                $imageData = file_get_contents($imagePath);
                $image = imagecreatefromstring($imageData);

                // Convert image to monochrome
                imagefilter($image, IMG_FILTER_GRAYSCALE);
                imagefilter($image, IMG_FILTER_CONTRAST, -100);

                $width = imagesx($image);
                $height = imagesy($image);
                $rowBytes = ceil($width / 8);
                $totalBytes = $rowBytes * $height;
                $binaryData = '';
                $xtmp = $x;
                $ytmp = $y;
                for ($ytmp = 0; $ytmp < $height; $ytmp++) {
                    for ($xtmp = 0; $xtmp < $width; $xtmp++) {
                        $pixel = imagecolorat($image, $xtmp, $ytmp);
                        $binaryData .= (imagecolorsforindex($image, $pixel)['red'] > 128) ? '0' : '1';
                    }
                    $binaryData .= str_repeat('0', $rowBytes * 8 - $width); // Padding for full byte
                }

                // Convert binary data to hexadecimal string
                $hexData = '';
                foreach (str_split($binaryData, 8) as $byte) {
                    $hexData .= sprintf('%02X', bindec($byte));
                }

                imagedestroy($image);

                $zpl .= "~DGimage.GRF,$totalBytes,$rowBytes,$hexData\n";
                $zpl .= "^FO$x,$y^XGimage.GRF,1,1^FS\n";
            } else {
                // Handle missing image file error
                $zpl .= "^FO$x,$y^FD[ERROR: Image file not found]^FS\n";
            }
            break;
    }
}

$zpl .= "\n^XZ"; // End of ZPL script

echo "success";
$file = 'output.zpl';
file_put_contents($file, $zpl);
