<?php

require_once '../NodeManager.php';

use App\NodeManager;

$nodeManager = NodeManager::getInstance();

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$newName = isset($_POST['newName']) ? trim($_POST['newName']) : '';

if ($id && !empty($newName)) {
    $nodeManager->renameNode($id, $newName);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
}
