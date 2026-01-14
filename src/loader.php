<?php

// Check if env exists
if (! file_exists('../.env')) {
    throw new RuntimeException('No .env file was found');
}

// Read the .env file
$lines = file('../.env',  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Loop line by line
foreach($lines as $line) {
    $line = trim($line);
    // Skip empty lines or comments
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    // Check for inline commnets
    if (preg_match('/\s+#/', $line)) {
        // Get key and value before comment
        $line = trim(preg_split('/\s+#/', $line, 2)[0]);
    };

    // Parse key and value
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);

    if ($key === '') {
        continue;
    }

    $_ENV[$key] = $value;
}
