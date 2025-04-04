<?php

// 🔥=== [1. Creating a connection to a database using PDO] ===🔥

// Ieslēdzam kļūdu ziņojumus, lai atvieglotu izstrādi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definējam datubāzes parametrus
define('DB_SERVER', 'localhost');
define('DB_NAME', 'posts_db');
define('DB_USERNAME', 'usserr');
define('DB_PASSWORD', 'password');

// Mēģinām izveidot savienojumu ar datubāzi, izmantojot PDO
try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Savienojuma kļūda ar datubāzi: " . $e->getMessage());
}


// 🔥=== [2. Extraction of posts] ===🔥

$sql_posts = "SELECT post_id, title, content FROM posts";

try {
    $stmt_posts = $pdo->query($sql_posts);
    $posts = array();

    while ($row = $stmt_posts->fetch(PDO::FETCH_ASSOC)) {
        $posts[$row["post_id"]] = array(
            "title" => $row["title"],
            "content" => $row["content"],
            "comments" => array(),
        );
    }
} catch (PDOException $e) {
    die("Kļūda izpildot vaicājumu pret tabulu 'posts': " . $e->getMessage());
}


// 🔥=== [3. Extracting comments and adding them to posts] ===🔥

$sql_comments = "SELECT comment_id, post_id, comment_content FROM comments";

try {
    $stmt_comments = $pdo->query($sql_comments);

    while ($row = $stmt_comments->fetch(PDO::FETCH_ASSOC)) {
        $post_id = $row["post_id"];
        if (isset($posts[$post_id])) {
            $posts[$post_id]["comments"][] = $row["comment_content"];
        }
    }
} catch (PDOException $e) {
    die("Kļūda izpildot vaicājumu pret tabulu 'comments': " . $e->getMessage());
}


// 🔥=== [4. Generation of HTML] ===🔥

echo "<html><head><title>Posts un Komentāri</title></head><body>";
echo "<ol>";

foreach ($posts as $post_id => $post) {
    echo "<li><strong>" . htmlspecialchars($post["title"]) . "</strong><br>";
    echo htmlspecialchars($post["content"]) . "<br>";

    if (!empty($post["comments"])) {
        echo "<ul>";
        foreach ($post["comments"] as $comment) {
            echo "<li>" . htmlspecialchars($comment) . "</li>";
        }
        echo "</ul>";
    }

    echo "</li>";
}

echo "</ol>";
echo "</body></html>";


// 🔥=== [5. Closing PDO connection (optional, but recommended)] ===🔥

$pdo = null;
