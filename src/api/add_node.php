<?php

require_once '../NodeManager.php';

use App\NodeManager;

$nodeManager = NodeManager::getInstance();

$parentId = isset($_POST['parentId']) && $_POST['parentId'] !== '' ? (int)$_POST['parentId'] : null;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';

if ($parentId === null && $name === 'Root') {
    // Create the root node if it doesn't exist
    try {
        $nodeManager->createRoot();
        echo json_encode(['status' => 'success', 'message' => 'Root node created.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($parentId !== null && !empty($name)) {
    // Add child node
    try {
        $nodeManager->addNode($parentId, $name);
        echo json_encode(['status' => 'success', 'message' => 'Child node added.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
}
