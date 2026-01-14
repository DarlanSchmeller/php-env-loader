<?php

// Check if env exists
if (! file_exists('../.env')) {
    throw new RuntimeException('No .env file was found');
}

// Read the .env file
$lines = file('../.env',  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Loop line by line
foreach ($lines as $line) {
    $line = trim($line);
    // Skip empty lines or comments
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    // Handle export keyword
    if (str_starts_with($line, 'export ')) {
        $line = substr($line, 7);
    }

    // Handle bad format
    if (! str_contains($line, '=')) {
        continue;
    }

    // Parse key and value
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);

    if ($key === '') {
        continue;
    }

    // Handle quote strings
    if (
        (str_starts_with($value, '"') && str_ends_with($value, '"') && strlen($value) >= 2) ||
        (str_starts_with($value, "'") && str_ends_with($value, "'") && strlen($value) >= 2)
    ) {
        $value = substr($value, 1, -1);
        $_ENV[$key] = $value;
        continue;
    }

    // Check for inline comments
    if (preg_match('/\s+#/', $value)) {
        // Get key and value before comment
        $value = trim(preg_split('/\s+#/', $value, 2)[0]);
    };

    // Set value to null if empty
    if ($value === '' || strtolower($value) === 'null') {
        $value = null;
        $_ENV[$key] = $value;
        continue;
    }

    // Cast value to boolean if necessary
    if (strtolower($value) === 'true' || strtolower($value) === 'false') {
        $value = (strtolower($value) === 'true')
            ? true
            : false;
        $_ENV[$key] = $value;
        continue;
    }

    // Cast value to numbers if necessary
    if (is_numeric($value)) {
        $value = ctype_digit($value)
            ? (int) $value
            : (float) $value;
        $_ENV[$key] = $value;
        continue;
    }

    // Store key and value in $_ENV as string
    $_ENV[$key] = $value;
}
