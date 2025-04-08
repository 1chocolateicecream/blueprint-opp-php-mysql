<!-- plans
1. Izveidot datubāzes savienojumu, izmantojot PDO
2. Izpildīt LEFT JOIN vaicājumu, lai iegūtu plakanu masīvu
3. Pārveidot plakano masīvu uz hierarhisku asociatīvo masīvu
4. Ģenerēt HTML sarakstu no asociatīvā masīva
5. Aizvērt PDO savienojumu (nav obligāti, bet ieteicams) -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Izveidot datubāzes savienojumu, izmantojot PDO
define('DB_SERVER', 'localhost');
define('DB_NAME', 'posts_db');
define('DB_USERNAME', 'usserr');
define('DB_PASSWORD', 'password');

try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Savienojuma kļūda: " . $e->getMessage());
}

// === KLASSES DEFINĪCIJAS ===
class Comment
{
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }
}

class Post
{
    public string $title;
    public string $content;
    public array $comments = [];

    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }
}

// 2. Izpildīt LEFT JOIN vaicājumu, lai iegūtu plakanu masīvu
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

// 3. Pārveidot plakano masīvu uz hierarhisku asociatīvo masīvu
$posts = [];

foreach ($rows as $row) {
    $post_id = $row['post_id'];

    if (!isset($posts[$post_id])) {
        $posts[$post_id] = new Post($row['title'], $row['content']);
    }

    if (!empty($row['comment_id'])) {
        $comment = new Comment($row['comment_content']);
        $posts[$post_id]->addComment($comment);
    }
}

// 4. Ģenerēt HTML sarakstu no asociatīvā masīva
echo "<html><head><title>Ziņas un komentāri</title></head><body>";
echo "<ol>";

foreach ($posts as $post) {
    echo "<li><strong>" . htmlspecialchars($post->title) . "</strong><br>";
    echo htmlspecialchars($post->content) . "<br>";

    if (!empty($post->comments)) {
        echo "<ul>";
        foreach ($post->comments as $comment) {
            echo "<li>" . htmlspecialchars($comment->content) . "</li>";
        }
        echo "</ul>";
    }

    echo "</li>";
}

echo "</ol>";
echo "</body></html>";

// 5. Aizvērt PDO savienojumu (nav obligāti, bet ieteicams)
$pdo = null;
?>