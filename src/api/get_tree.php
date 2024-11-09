<?php

require_once '../NodeManager.php';

use App\NodeManager;

$nodeManager = NodeManager::getInstance();

echo json_encode($nodeManager->getTreeStructure());
