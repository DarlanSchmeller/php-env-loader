# 🪴 PHP Env Loader [![.env](https://img.shields.io/badge/.env-Loader-%2300C853?logo=environment&logoColor=white&style=for-the-badge)]()

[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.0-blue?logo=php&logoColor=white)](https://www.php.net/)

A lightweight, object-oriented PHP library to load `.env` files into `$_ENV` with **automatic type casting** and **quote handling**. Perfect for small projects, scripts, or when you want a simple alternative to `vlucas/phpdotenv`.

---

## Features

- Load `.env` files into `$_ENV` automatically  
- Supports `export` keyword  
- Inline comment handling (`# comment`)  
- Quote string handling (`"string"` or `'string'`)  
- Automatic type casting:  
  - `"true"` / `"false"` → `bool`  
  - Numeric strings → `int` or `float`  
  - `"null"` → `null`  
- Static helper method for convenience  

---

## Installation

Clone the repository:

```bash
git clone https://github.com/DarlanSchmeller/php-env-loader.git
```

Include in your project:
```php
require __DIR__ . '/src/EnvLoader.php';
use Src\EnvLoader;
```

---

## Usage

### Basic Usage 

```php
$env = new EnvLoader(); // Defaults to '../.env'
$variables = $env->load();

var_dump($variables); // Loaded env variables
var_dump($_ENV);      // Also accessible globally
```

### Static Convenience

If you just want to load a `.env` without instantiating the class:

```php
$variables = EnvLoader::loadFrom(__DIR__ . '/.env');
```


### Sample .env

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=root
DB_PASSWORD="supersecret"
APP_DEBUG=true
TIMEOUT=5.5
```
After loading
```php
$_ENV['DB_PORT'];      // int(3306)
$_ENV['DB_PASSWORD'];  // string("supersecret")
$_ENV['APP_DEBUG'];    // bool(true)
$_ENV['TIMEOUT'];      // float(5.5)
```

---

> [!NOTE]
> - Inline comments are supported outside quotes, e.g., `DB_HOST=127.0.0.1 # main database host`
> - Quotes around values are stripped automatically
> - Empty strings and "null" values are converted to null 
 