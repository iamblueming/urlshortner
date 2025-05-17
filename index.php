<?php
session_start();
$databaseFile = 'database.txt';

function generateShortPath($length, $existingPaths) {
    do {
        $shortPath = '/' . str_pad(rand(0, pow(10, $length) - 1), $length, "0", STR_PAD_LEFT);
    } while (in_array($shortPath, $existingPaths)); 
    return $shortPath;
}

function getUserIP() { // cf real ip incase u use cf proxy
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// actually you can use any path like /dhf
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH); // handles ?params cleanly

if ($requestPath !== '/' && file_exists($databaseFile)) {
    $database = file($databaseFile, FILE_IGNORE_NEW_LINES);
    foreach ($database as $index => $line) {
        if ($index === 0) continue;
        list($fullUrl, $shortPath, $createdTime, $usedAccessCode, $ipUsed, $clickCount) = explode('|', $line);
        if ($shortPath === $requestPath) {
            $clickCount++;
            $database[$index] = "$fullUrl|$shortPath|$createdTime|$usedAccessCode|$ipUsed|$clickCount";
            file_put_contents($databaseFile, implode("\n", $database));
            header("Location: $fullUrl", true, 302);
            exit;
        }
    }
    http_response_code(404);
    echo "Shortened URL not found.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullUrl = trim($_POST['fullurl']);
    $pathLength = $_POST['path_length'] == '4' ? 4 : 5;
    $accessCode = trim($_POST['accesscode']);
    $clientIP = getUserIP();
    $createdTime = time();

    // prevent collapse
    $existingPaths = [];
    $database = file_exists($databaseFile) ? file($databaseFile, FILE_IGNORE_NEW_LINES) : [];
    $validAccessCodes = explode(',', trim($database[0] ?? ''));

    if (!in_array($accessCode, $validAccessCodes)) {
        echo "Invalid access code.";
        exit;
    }

    foreach ($database as $index => $line) {
        if ($index === 0) continue;
        $parts = explode('|', $line);
        if (count($parts) >= 2) {
            $existingPaths[] = trim($parts[1]);
        }
    }

    $shortPath = generateShortPath($pathLength, $existingPaths);

    // txt is the best db
    $entry = "$fullUrl|$shortPath|$createdTime|$accessCode|$clientIP|0";
    file_put_contents($databaseFile, "$entry\n", FILE_APPEND);
    echo "Shortened URL: <a href='https://fur.hk$shortPath'>https://fur.hk$shortPath</a>"; // change required
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>donthatefurry</title> <!--change title to other cuz i know you are not furry (or are you?)-->
</head>
<body>
    <h2>url shorter</h2>
    <form method="post">
        fullurl <input type="text" name="fullurl" required><br>
        path_length <select name="path_length">
            <option value="4">4</option>
            <option value="5">5</option>
        </select><br>
        accescode <input type="text" name="accesscode" required><br>
        <input type="submit" value="Shorten">
    </form>
    <br>
    <a href="https://github.com/iamblueming/urlshortner">GitHub</a>
</body>
</html>
