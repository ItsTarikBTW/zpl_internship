# ZPL Label Generator

This project provides a web interface for generating ZPL (Zebra Programming Language) labels. It allows users to select values for predefined variables in a ZPL template and generate customized labels.

## Files Description

- `README.md`: This file, containing project information and setup instructions.
- `output.zpl`: The original ZPL template file with placeholders for variables.
- `updated_output.zpl`: The generated ZPL file after replacing placeholders with user-selected values.
- `selectform.php`: A PHP script that displays a form for users to select values for variables defined in the `output.zpl` file.
- `stdToLabel.php`: A PHP script that processes the form submission from `selectform.php`, replaces placeholders in `output.zpl` with user-selected values, and saves the result to `updated_output.zpl`.
- `xmlToZpl.php`: A PHP script intended for converting XML data to ZPL format (not detailed in the provided excerpts).

## How It Works

1. The user navigates to `selectform.php`, which reads the `output.zpl` file and displays a form for selecting values for the placeholders defined in the ZPL template.
2. Upon form submission, `stdToLabel.php` processes the input, replaces the placeholders in `output.zpl` with the selected values, and saves the customized ZPL code to `updated_output.zpl`.
3. The `updated_output.zpl` file can then be used to print labels using a Zebra printer.

## Setup

1. Ensure you have a PHP server environment set up.
2. Place all files in the project directory within your server's document root.
3. Access `selectform.php` through your web browser to start generating custom ZPL labels.

## Dependencies

- Tailwind CSS is used for styling the form in `selectform.php` and is included via CDN.

## Note

This project is a basic implementation and might require adjustments for production use, including security enhancements and error handling improvements.