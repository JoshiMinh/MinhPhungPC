<?php
include 'db.php';
include 'web_sections/categoryMap.php';

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

if ($table && $id) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header("HTTP/1.0 404 Not Found");
        exit("<h1>Item Not Found</h1>");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $userId = $_SESSION['user_id'] ?? '';
        $content = trim($_POST['comment']);

        if ($userId && $content) {
            $insertStmt = $pdo->prepare("INSERT INTO comments (user_id, product_id, product_table, content, time) VALUES (:user_id, :product_id, :product_table, :content, NOW())");
            $insertStmt->execute([
                'user_id' => $userId,
                'product_id' => $id,
                'product_table' => $table,
                'content' => $content
            ]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?table=$table&id=$id");
            exit;
        }
    }

    $commentStmt = $pdo->prepare("
        SELECT comments.content, comments.time, users.name, users.profile_image
        FROM comments
        JOIN users ON comments.user_id = users.user_id
        WHERE comments.product_id = :product_id AND comments.product_table = :product_table
        ORDER BY comments.comment_id DESC
    ");
    $commentStmt->execute(['product_id' => $id, 'product_table' => $table]);
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    exit("<h1>Invalid request</h1>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['name']) ?> - MinhPhungPC</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .additional-details p {
            margin-bottom: 5px;
        }
        .additional-details ul {
            padding-left: 20px;
        }
        .additional-details li {
            list-style-type: disc;
        }
    </style>
</head>
<body style="transition: 0.5s;">
    <?php include 'web_sections/navbar.php'; ?>
    <main class="container my-4">
        <?php include 'web_sections/add_to_cart.php'; ?>
        <div class="row">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($item['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($item['name']) ?>">
            </div>
            <div class="col">
                <div class="card p-3 bg-white text-dark">
                    <h2><?= htmlspecialchars($item['name']) ?></h2>
                    <p class="card-text"><?= number_format($item['price'], 0, ',', '.') . '₫' ?></p>
                    <p class="card-text"><strong>Brand:</strong> <?= htmlspecialchars($item['brand']) ?></p>
                    <form method="post">
                        <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                        <button type="submit" class="btn btn-primary w-100 h-100 border-0">Add to Cart</button>
                    </form>
                </div>
                <div class="additional-details mt-3 p-3 card bg-white text-dark">
                    <h4>Description</h4>
                    <ul>
                        <?php foreach ($item as $key => $value): ?>
                            <?php if (!in_array($key, ['id', 'name', 'price', 'image', 'brand'])): ?>
                                <li><strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div id="comment" class="mt-5">
            <h4>Comments</h4>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" class="mb-4">
                    <div class="form-group">
                        <label for="comment">Add a comment:</label>
                        <textarea name="comment" id="comment" rows="3" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
            <?php else: ?>
                <p><a href="account.php">Log in</a> to post a comment.</p>
            <?php endif; ?>

            <?php if ($comments): ?>
                <ul class="list-unstyled">
                    <?php foreach ($comments as $comment): ?>
                        <li class="media my-3 p-3 rounded" style="background-color: var(--bg-elevated); box-shadow: var(--card-shadow);">
                            <img src="<?= htmlspecialchars($comment['profile_image']) ?>" alt="<?= htmlspecialchars($comment['name']) ?>" class="mr-3 rounded-circle" style="width: 50px; height: 50px;">
                            <div class="media-body">
                                <h5 class="mt-0 mb-1" style="color: var(--text-primary);"><?= htmlspecialchars($comment['name']) ?> <small style="color: var(--text-secondary);"><?= date("F j, Y, g:i a", strtotime($comment['time'])) ?></small></h5>
                                <p style="color: var(--text-primary);"><?= $comment['content'] ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'web_sections/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="darkmode.js"></script>
</body>
</html>