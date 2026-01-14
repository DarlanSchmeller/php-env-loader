<?php

// Read the .env file
$lines = file('../.env', FILE_SKIP_EMPTY_LINES);
$envValues = [];

// Loop line by line
foreach($lines as $line) {
    $line = trim($line);

    # Skip empty lines or comments
    if ($line === '' || str_starts_with('#', $line)) {
        continue;
    }

    // Parse key and value
    [$key, $value] = explode('=', $line, 2);
    trim($key);
    trim($value);

    if (empty($key) || empty($value)) {
        continue;
    }

    $_ENV[$key] = $value;
}
