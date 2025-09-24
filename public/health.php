<?php
// Debug health check endpoint
header('Content-Type: application/json');

try {
    // Basic PHP check
    $status = [
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'environment' => [
            'APP_ENV' => getenv('APP_ENV'),
            'DB_CONNECTION' => getenv('DB_CONNECTION'),
            'DB_HOST' => getenv('DB_HOST') ? 'SET' : 'NOT_SET',
            'DB_DATABASE' => getenv('DB_DATABASE') ? 'SET' : 'NOT_SET',
        ]
    ];
    
    // Test database connection if Laravel is available
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        
        try {
            $pdo = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            $status['database'] = 'connected';
        } catch (Exception $e) {
            $status['database'] = 'failed: ' . $e->getMessage();
        }
    }
    
    http_response_code(200);
    echo json_encode($status, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}