<?php

require_once '../NodeManager.php';

use App\NodeManager;

$nodeManager = NodeManager::getInstance();

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

if ($id) {
    $nodeManager->deleteNode($id);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
}
