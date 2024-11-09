CREATE DATABASE IF NOT EXISTS tree_app;
USE tree_app;

CREATE TABLE IF NOT EXISTS nodes
(
    id        INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT          NULL,
    name      VARCHAR(255) NOT NULL,
    is_leaf   BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (parent_id) REFERENCES nodes (id) ON DELETE CASCADE
);
