<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"] ?? '';
    $content = $_POST["content"] ?? '';
    $comment = $_POST["comment"] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->execute([$title, $content]);
        $post_id = $pdo->lastInsertId();

        if (!empty($comment)) {
            $stmt2 = $pdo->prepare("INSERT INTO comments (post_id, comment_content) VALUES (?, ?)");
            $stmt2->execute([$post_id, $comment]);
        }

        header("Location: jauns-posts-comments-assoc-ary.php");
        exit;
    } catch (PDOException $e) {
        die("Kļūda saglabājot datus: " . $e->getMessage());
    }
}
