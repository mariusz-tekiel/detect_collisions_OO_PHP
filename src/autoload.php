<?php
declare(strict_types=1);

spl_autoload_register(function(string $class): void {
    $prefix = 'App\\';
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR;

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
