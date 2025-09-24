<?php
// Simple environment test - no Laravel required
header('Content-Type: application/json');

try {
    $response = [
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'current_directory' => getcwd(),
        'files_exist' => [
            'vendor/autoload.php' => file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO',
            'bootstrap/app.php' => file_exists(__DIR__ . '/../bootstrap/app.php') ? 'YES' : 'NO',
            '.env' => file_exists(__DIR__ . '/../.env') ? 'YES' : 'NO',
            'artisan' => file_exists(__DIR__ . '/../artisan') ? 'YES' : 'NO',
        ],
        'environment_vars' => [
            'APP_ENV' => getenv('APP_ENV') ?: 'NOT_SET',
            'APP_KEY' => getenv('APP_KEY') ? 'SET (length: ' . strlen(getenv('APP_KEY')) . ')' : 'NOT_SET',
            'DB_CONNECTION' => getenv('DB_CONNECTION') ?: 'NOT_SET',
            'DB_HOST' => getenv('DB_HOST') ? 'SET' : 'NOT_SET',
            'PORT' => getenv('PORT') ?: 'NOT_SET',
        ],
        'directory_listing' => array_slice(scandir(__DIR__ . '/..'), 0, 20),
    ];
    
    // Try to test database connection if DB vars are set
    if (getenv('DB_CONNECTION') === 'mysql' && getenv('DB_HOST')) {
        try {
            $pdo = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                [PDO::ATTR_TIMEOUT => 5]
            );
            $response['database_test'] = 'SUCCESS - Connected to MySQL';
        } catch (Exception $e) {
            $response['database_test'] = 'FAILED: ' . $e->getMessage();
        }
    } else {
        $response['database_test'] = 'SKIPPED - DB variables not properly set';
    }
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>