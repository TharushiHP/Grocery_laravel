<?php
// Simple health check
header('Content-Type: application/json');
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'app' => 'Grocery Store Laravel'
]);