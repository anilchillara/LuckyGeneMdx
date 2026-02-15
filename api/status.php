<?php
header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'timestamp' => time(),
    'service' => 'LuckyGeneMdx API',
    'version' => '1.0.0'
];

echo json_encode($status);
