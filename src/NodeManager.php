<?php

namespace App;

use PDO;
use PDOException;

/**
 * Class NodeManager
 *
 * Singleton class for managing CRUD operations for tree nodes in a MySQL database.
 */
class NodeManager
{
    /**
     * @var NodeManager|null Singleton instance
     */
    private static ?NodeManager $instance = null;

    /**
     * @var PDO Database connection instance
     */
    private PDO $pdo;

    /**
     * NodeManager constructor (private for Singleton pattern).
     *
     * @param array $config Database configuration.
     */
    private function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8";
        $this->pdo = new PDO($dsn, $config['username'], $config['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get the Singleton instance of NodeManager.
     *
     * @return NodeManager
     */
    public static function getInstance(): NodeManager
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/config.php';
            self::$instance = new NodeManager($config['db']);
        }
        return self::$instance;
    }

    /**
     * Creates the root node in the database.
     *
     * @return void
     */
    public function createRoot(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO nodes (parent_id, name, is_leaf) VALUES (NULL, 'Root', FALSE)");
        $stmt->execute();
    }

    /**
     * Adds a new node under a specified parent.
     *
     * @param int $parentId Parent node ID.
     * @param string $name Name of the new node.
     * @return void
     */
    public function addNode(int $parentId, string $name): void
    {
        // Check parent.
        if ($parentId !== null) {
            $checkParentStmt = $this->pdo->prepare("SELECT COUNT(*) FROM nodes WHERE id = ?");
            $checkParentStmt->execute([$parentId]);
            $parentExists = $checkParentStmt->fetchColumn();

            if (!$parentExists) {
                throw new PDOException("Parent node with ID $parentId does not exist.");
            }
        }

        // Add new node.
        $stmt = $this->pdo->prepare("INSERT INTO nodes (parent_id, name, is_leaf) VALUES (?, ?, TRUE)");
        $stmt->execute([$parentId, $name]);

        // Updating the parent node to indicate that it is no longer a leaf.
        if ($parentId !== null) {
            $updateStmt = $this->pdo->prepare("UPDATE nodes SET is_leaf = FALSE WHERE id = ?");
            $updateStmt->execute([$parentId]);
        }
    }

    /**
     * Deletes a node and all its children.
     *
     * @param int $id Node ID to delete.
     * @return void
     */
    public function deleteNode(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM nodes WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Renames a node with a new name.
     *
     * @param int $id Node ID to rename.
     * @param string $newName New name for the node.
     * @return void
     */
    public function renameNode(int $id, string $newName): void
    {
        $stmt = $this->pdo->prepare("UPDATE nodes SET name = ? WHERE id = ?");
        $stmt->execute([$newName, $id]);
    }

    /**
     * Retrieves the tree structure.
     *
     * @return array Tree structure as an associative array.
     */
    public function getTreeStructure(): array
    {
        $nodes = $this->pdo->query("SELECT * FROM nodes ORDER BY parent_id ASC")->fetchAll(PDO::FETCH_ASSOC);
        return $this->buildTree($nodes);
    }

    /**
     * Recursively build a nested tree structure.
     *
     * @param array $nodes Flat array of nodes from the database.
     * @param int|null $parentId Parent ID to start building from.
     * @return array Nested tree structure.
     */
    private function buildTree(array $nodes, int $parentId = null): array
    {
        $branch = [];
        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $children = $this->buildTree($nodes, $node['id']);
                if ($children) {
                    $node['children'] = $children;
                }
                $branch[] = $node;
            }
        }
        return $branch;
    }
}
