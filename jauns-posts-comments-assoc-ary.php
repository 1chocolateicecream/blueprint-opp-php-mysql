<!-- plans
1. Izveidot datubāzes savienojumu, izmantojot PDO
2. Izpildīt LEFT JOIN vaicājumu, lai iegūtu plakanu masīvu
3. Pārveidot plakano masīvu uz hierarhisku asociatīvo masīvu
4. Ģenerēt HTML sarakstu no asociatīvā masīva
5. Aizvērt PDO savienojumu (nav obligāti, bet ieteicams) -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definējam savienojuma parametrus
define('DB_SERVER', 'localhost');
define('DB_NAME', 'posts_db');
define('DB_USERNAME', 'usserr');
define('DB_PASSWORD', 'password');

try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Konekcija veiksmīga!";
} catch (PDOException $e) {
    die("Savienojuma kļūda: " . $e->getMessage());
}

$sql = "
SELECT 
    p.post_id, p.title, p.content, 
    c.comment_id, c.comment_content
FROM posts p
LEFT JOIN comments c ON p.post_id = c.post_id
";

try {
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Vaicājuma kļūda: " . $e->getMessage());
}

$posts = [];

foreach ($rows as $row) {
    $post_id = $row['post_id'];

    // Ja ziņa vēl nav saglabāta, saglabājam to
    if (!isset($posts[$post_id])) {
        $posts[$post_id] = [
            "title" => $row["title"],
            "content" => $row["content"],
            "comments" => []
        ];
    }

    // Ja komentārs eksistē, pievienojam to
    if (!empty($row["comment_id"])) {
        $posts[$post_id]["comments"][] = $row["comment_content"];
    }
}

echo "<html><head><title>Ziņas un komentāri</title></head><body>";
echo "<ol>";

foreach ($posts as $post) {
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

$pdo = null;
