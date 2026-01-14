<?php

namespace Src;

class EnvLoader
{
    protected $path;
    protected array $variables = [];

    public function __construct(string $path = '../.env')
    {
        $this->path = $path;
    }

    public function load(): void
    {
        // Check if env exists
        if (! file_exists($this->path)) {
            throw new \RuntimeException('No .env file was found on the following path: ' . $this->path);
        }

        // Read the .env file
        $lines = file($this->path,  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $this->parseLine(trim($line));
        }

        // Load variables into $_ENV
        foreach ($this->variables as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    protected function parseLine(string $line): void
    {

        // Skip empty lines or comments
        if ($line === '' || str_starts_with($line, '#')) {
            return;
        }

        // Handle export keyword
        if (str_starts_with($line, 'export ')) {
            $line = substr($line, 7);
        }

        // Handle bad format
        if (! str_contains($line, '=')) {
            return;
        }

        // Parse key and value
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($key === '') {
            return;
        }

        // Handle quote strings
        if ($this->isQuoted($value)) {
            $value = substr($value, 1, -1);
            $this->variables[$key] = $value;
            return;
        }

        // Check for inline comments
        if (preg_match('/\s+#/', $value)) {
            // Get key and value before comment
            $value = trim(preg_split('/\s+#/', $value, 2)[0]);
        };

        // Set value to null if empty
        if ($value === '' || strtolower($value) === 'null') {
            $value = null;
            $this->variables[$key] = $value;
            return;
        }

        // Cast value to boolean if necessary
        if (strtolower($value) === 'true' || strtolower($value) === 'false') {
            $value = (strtolower($value) === 'true')
                ? true
                : false;
            $this->variables[$key] = $value;
            return;
        }

        // Cast value to numbers if necessary
        if (is_numeric($value)) {
            $value = ctype_digit($value)
                ? (int) $value
                : (float) $value;
            $this->variables[$key] = $value;
            return;
        }

        // Store key and value in $_ENV as string
        $this->variables[$key] = $value;
    }

    private function isQuoted(string $value): bool
    {
        return (
            (str_starts_with($value, '"') && str_ends_with($value, '"') && strlen($value) >= 2) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'") && strlen($value) >= 2)
        );
    }
}
