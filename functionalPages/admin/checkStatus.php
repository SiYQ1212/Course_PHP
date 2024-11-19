<?php
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$command = '';

if ($type === 'course') {
    $command = 'python ../../auxiliaryProgram/admin.py course';
} elseif ($type === 'proxy') {
    $command = 'python ../../auxiliaryProgram/admin.py proxy';
} else {
    die(json_encode([]));
}

$output = shell_exec($command);
$emails = json_decode($output, true);

if (is_array($emails)) {
    echo json_encode($emails);
} else {
    echo json_encode([]);
}