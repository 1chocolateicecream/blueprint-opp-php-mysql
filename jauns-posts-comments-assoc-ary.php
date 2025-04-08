<!-- plans
1. Izveidot datubāzes savienojumu, izmantojot PDO
2. Izpildīt LEFT JOIN vaicājumu, lai iegūtu plakanu masīvu
3. Pārveidot plakano masīvu uz hierarhisku asociatīvo masīvu
4. Ģenerēt HTML sarakstu no asociatīvā masīva
5. Aizvērt PDO savienojumu (nav obligāti, bet ieteicams) -->

<?php
require_once 'db.php';

// === Klases ===
class Comment {
    public string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }
}

class Post {
    public string $title;
    public string $content;
    public array $comments = [];

    public function __construct(string $title, string $content) {
        $this->title = $title;
        $this->content = $content;
    }

    public function addComment(Comment $comment): void {
        $this->comments[] = $comment;
    }

    public function display(): void {
        echo "<li><strong>" . htmlspecialchars($this->title) . "</strong><br>";
        echo htmlspecialchars($this->content) . "<br>";

        if (!empty($this->comments)) {
            echo "<ul>";
            foreach ($this->comments as $comment) {
                echo "<li>" . htmlspecialchars($comment->content) . "</li>";
            }
            echo "</ul>";
        }

        echo "</li>";
    }
}

// === Vaicājums ===
$sql = "
SELECT 
    p.post_id, p.title, p.content, 
    c.comment_id, c.comment_content
FROM posts p
LEFT JOIN comments c ON p.post_id = c.post_id
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$posts = [];
foreach ($rows as $row) {
    $post_id = $row['post_id'];

    if (!isset($posts[$post_id])) {
        $posts[$post_id] = new Post($row['title'], $row['content']);
    }

    if (!empty($row['comment_id'])) {
        $posts[$post_id]->addComment(new Comment($row['comment_content']));
    }
}

echo "<html><head><title>Ziņas un komentāri</title></head><body>";
echo "<h1>Ziņas</h1><ol>";
foreach ($posts as $post) {
    $post->display();
}
echo "</ol>";

// === Forma ===
require_once 'form.php';

echo "</body></html>";

$pdo = null;
?>
