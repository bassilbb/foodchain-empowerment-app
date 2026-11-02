<?php
<?php
// Try different database names
$databases = ['foodchain-marketplace'];
foreach ($databases as $db) {
    echo "Trying database: $db\n";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=$db", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query("DESCRIBE page_contents");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Columns: " . implode(", ", array_column($columns, 'Field')) . "\n";
        
        // Try to update is_active for supplier
        $updateStmt = $pdo->prepare("UPDATE page_contents SET is_active = 1 WHERE page_key = ?");
        $updateStmt->execute(['supplier']);
        echo "Updated rows: " . $updateStmt->rowCount() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
echo "Done";