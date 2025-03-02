<?php
if ($_SERVER['REQUEST_URI'] === 'dash.php' || basename($_SERVER['PHP_SELF']) === 'dash.php') {
    $databaseFile = 'database.txt';
    if (!file_exists($databaseFile)) {
        echo "No data available.";
        exit;
    }
    
    $database = file($databaseFile, FILE_IGNORE_NEW_LINES);
    echo "<h2>admin panel</h2>";
    echo "<h3>Access Codes</h3><pre>" . htmlspecialchars($database[0]) . "</pre>";
    
    echo "<h3>Shortened URLs</h3>";
    echo "<table border='1'><tr><th>Full URL</th><th>Short Path</th><th>Created Time</th><th>Used Access Code</th><th>IP Used</th><th>Click Count</th></tr>";
    
    foreach (array_slice($database, 1, 20) as $line) {
        $parts = explode('|', $line);
        if (count($parts) < 6) continue; // Skip invalid lines

        list($fullUrl, $shortPath, $createdTime, $usedAccessCode, $ipUsed, $clickCount) = $parts;
        echo "<tr><td>$fullUrl</td><td><a href='https://fur.hk$shortPath'>$shortPath</a></td><td>" . date('Y-m-d H:i:s', $createdTime) . "</td><td>$usedAccessCode</td><td>$ipUsed</td><td>$clickCount</td></tr>";
    }
    echo "</table>";
    exit;
}
?>
