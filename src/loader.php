<?php

// Check if env exists
if (! file_exists('../.env')) {
    throw new RuntimeException('No .env file was found');
}

// Read the .env file
$lines = file('../.env',  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$envValues = [];

// Loop line by line
foreach($lines as $line) {
    $line = trim($line);

    // Skip empty lines or comments
    if ($line === '' || str_starts_with('#', $line)) {
        continue;
    }

    // Check for inline commnets
    if (str_contains($line, '#')) {
        // Get key and value before comment
        $line = trim(explode('#', $line)[0]);
    };

    // Parse key and value
    [$key, $value] = explode('=', $line, 2);
    trim($key);
    trim($value);

    if ($key === '' || $value === '') {
        continue;
    }

    $_ENV[$key] = $value;
}
