<?php
// Standalone health check - no Laravel dependencies
header('Content-Type: application/json');

$response = [
    'status' => 'alive',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'env_check' => [
        'APP_KEY' => getenv('APP_KEY') ? 'SET' : 'MISSING',
        'DB_CONNECTION' => getenv('DB_CONNECTION') ?: 'NOT_SET',
        'DB_HOST' => getenv('DB_HOST') ? 'SET' : 'MISSING',
        'PORT' => getenv('PORT') ?: 'NOT_SET'
    ]
];

// Try basic database connection if MySQL variables are set
if (getenv('DB_CONNECTION') === 'mysql' && getenv('DB_HOST')) {
    try {
        $pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_TIMEOUT => 3]
        );
        $response['database'] = 'CONNECTED';
    } catch (Exception $e) {
        $response['database'] = 'FAILED: ' . $e->getMessage();
    }
} else {
    $response['database'] = 'NOT_CONFIGURED';
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>