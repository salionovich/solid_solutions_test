
# Tree Structure Management

This is a web-based tree structure management application that allows users to create, rename, delete, and manage hierarchical nodes. It uses PHP, MySQL, JavaScript, jQuery, and Bootstrap for the frontend.

## Features

- Create root and child nodes
- Rename nodes
- Delete nodes with a countdown timer for confirmation
- Expand and collapse nodes to show or hide children

## Requirements

- PHP (>=7.4)
- MySQL
- Web server (e.g., Apache, Nginx, or use a local environment like XAMPP, MAMP)
- Composer (optional, if you want to manage dependencies)

## Installation

1. **Unzip file to directory**:
   ```bash
   cd tree-management
   ```

2. **Set up the Database**:

   - Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line).
   - Create a new database, e.g., `tree_app`.
   - Import the following SQL to create the `nodes` table:

     ```sql
     CREATE DATABASE IF NOT EXISTS tree_app;
     USE tree_app;

     CREATE TABLE IF NOT EXISTS nodes (
         id INT AUTO_INCREMENT PRIMARY KEY,
         parent_id INT NULL,
         name VARCHAR(255) NOT NULL,
         is_leaf BOOLEAN DEFAULT TRUE,
         FOREIGN KEY (parent_id) REFERENCES nodes(id) ON DELETE CASCADE
     );
     ```

3. **Configure Database Connection**:

   - Open `src/config.php`.
   - Edit the database credentials to match your setup:

     ```php
     return [
         'db' => [
             'host' => 'localhost',
             'dbname' => 'tree_app',
             'username' => 'your_db_username',
             'password' => 'your_db_password',
         ],
     ];
     ```

4. **Set Up Web Server**:

   - Place the project in your web server's root directory (e.g., `htdocs` for XAMPP).
   - Make sure your web server is running.

5. **Access the Application**:

   - Open a browser and go to `http://localhost/tree-management/public/index.html`.

6. **Using the Application**:

   - Use the "Create Root" button to add the initial root node.
   - Expand and collapse nodes using the arrow buttons.
   - Double-click on a node's name to rename it.
   - Use the "+" button to add child nodes.
   - Use the "-" button to delete a node (with confirmation and countdown timer).

## Project Structure

```
project-root/
├── src/
│   ├── NodeManager.php      # Main class for managing tree nodes
│   ├── config.php           # Database configuration
│   ├── api/                 # API endpoints for AJAX calls
│   │   ├── add_node.php
│   │   ├── delete_node.php
│   │   ├── rename_node.php
│   │   └── get_tree.php
├── index.html           # Main frontend file
├── db/
│   └── tree_app.sql     # DB dump file
├── css/
│   └── styles.css       # Custom styles
├── js/
│   └── tree.js          # JavaScript for managing tree functionality
└── README.md            # Project documentation
```

## API Endpoints

The application uses AJAX to interact with the following PHP scripts:

- **add_node.php**: Adds a new node to the database.
- **delete_node.php**: Deletes a node and its children.
- **rename_node.php**: Renames a node.
- **get_tree.php**: Fetches the entire tree structure from the database.

## Troubleshooting

- Make sure your MySQL server is running and the credentials in `config.php` are correct.
- Ensure that your PHP version meets the minimum requirements.
- Check file permissions if you're having issues with the web server accessing files.
- If using XAMPP or MAMP, ensure the project is placed in the appropriate root directory (e.g., `htdocs` for XAMPP).

## Contributing

Feel free to fork the repository and submit pull requests with improvements or fixes.

## License

MIT License
