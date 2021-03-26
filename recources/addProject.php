<?php
header("Content-type: application/json");

$Name = isset($_POST['name']) ? $_POST['name'] : null;

if(!$Name) return;

echo json_encode([
    'status' => 1,
    'data' => [
        'name' => $Name
    ]
]);
?>