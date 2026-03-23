<?php
include 'core/config.php';
include 'core/helpers.php';

$type = $_GET['table'] ?? ''; // type slug
$id = $_GET['id'] ?? '';

if ($type && $id) {
    $stmt = $pdo->prepare("SELECT p.*, b.name as brand FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = :id");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header("HTTP/1.0 404 Not Found");
        exit("<h1>Item Not Found</h1>");
    }

    // Decode specs and merge into item for display loop
    $specs = json_decode($item['specs'], true) ?: [];
    $item = array_merge($item, $specs);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['comment']) && isset($_SESSION['user_id'])) {
            $insertStmt = $pdo->prepare("INSERT INTO comments (user_id, product_id, content, created_at) VALUES (:user_id, :product_id, :content, CURRENT_TIMESTAMP)");
            $insertStmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $id,
                'content' => trim($_POST['comment'])
            ]);
            header("Location: {$_SERVER['PHP_SELF']}?table=$type&id=$id");
            exit;
        }

        if (isset($_POST['rating']) && is_numeric($_POST['rating'])) {
            $rating = (int)$_POST['rating'];
            if ($rating >= 1 && $rating <= 5 && isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                // Use reviews table instead of ratings column
                $stmt = $pdo->prepare("SELECT review_id FROM reviews WHERE user_id = :user_id AND product_id = :product_id");
                $stmt->execute(['user_id' => $userId, 'product_id' => $id]);
                $existingReview = $stmt->fetchColumn();

                if ($existingReview) {
                    $updateStmt = $pdo->prepare("UPDATE reviews SET rating = :rating WHERE review_id = :review_id");
                    $updateStmt->execute(['rating' => $rating, 'review_id' => $existingReview]);
                } else {
                    $insertStmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating) VALUES (:user_id, :product_id, :rating)");
                    $insertStmt->execute(['user_id' => $userId, 'product_id' => $id, 'rating' => $rating]);
                }
                echo "<script>window.location.href = window.location.href;</script>";
                exit;
            }
        }
    }

    $commentStmt = $pdo->prepare("SELECT comments.content, comments.created_at as time, users.name, users.profile_image FROM comments JOIN users ON comments.user_id = users.user_id WHERE comments.product_id = :product_id ORDER BY comments.comment_id DESC");
    $commentStmt->execute(['product_id' => $id]);
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch average rating and count from reviews table
    $ratingStmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = ?");
    $ratingStmt->execute([$id]);
    $ratingStats = $ratingStmt->fetch(PDO::FETCH_ASSOC);
    $averageRating = (float)$ratingStats['avg_rating'];
    $userCount = (int)$ratingStats['count'];

    $userRating = null;
    if (isset($_SESSION['user_id'])) {
        $userRatingStmt = $pdo->prepare("SELECT rating FROM reviews WHERE user_id = ? AND product_id = ?");
        $userRatingStmt->execute([$_SESSION['user_id'], $id]);
        $userRating = $userRatingStmt->fetchColumn();
    }
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
    <link rel="icon" href="../storage/images/icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .additional-details p { margin-bottom: 5px; }
        .additional-details ul { padding-left: 20px; }
        .additional-details li { list-style-type: disc; }
        .ratings .fa-star { font-size: 1.5rem; color: #ccc; }
        .ratings .star.fas.fa-star, .ratings .star.hover { color: #ffcc00; }
        .ratings .star { cursor: pointer; }
    </style>
</head>
<body>
    <?php include 'ui/navbar.php'; ?>
    <main class="container my-4">
        <?php include 'core/cart_add.php'; ?>
        <div class="row">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($item['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($item['name']) ?>">
            </div>
            <div class="col">
                <div class="card p-3 bg-white text-dark">
                   <h2><?= htmlspecialchars($item['name']) ?></h2>
                    <div class="ratings mb-2" id="rating-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="star <?= ($userRating !== null && $i < $userRating) || ($userRating === null && $i < floor($averageRating)) ? 'fas fa-star' : 'fa fa-star' ?>" data-index="<?= $i ?>"></span>
                        <?php endfor; ?>
                        <span>(<?= $userCount ?>)</span>
                    </div>

                    <p class="card-text"><?= number_format($item['price'], 0, ',', '.') . '₫' ?></p>
                    <p class="card-text"><strong>Brand:</strong> <?= htmlspecialchars($item['brand']) ?></p>
                    <form method="post">
                        <input type="hidden" name="table" value="<?= htmlspecialchars($type) ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['product_id']) ?>">
                        <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                    </form>
                </div>
                <div class="additional-details mt-3 p-3 card bg-white text-dark">
                    <h4>Description</h4>
                    <ul>
                        <?php foreach ($item as $key => $value): ?>
                            <?php if (!in_array($key, ['product_id', 'brand_id', 'name', 'price', 'image', 'brand', 'type', 'specs', 'created_at'])): ?>
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
                <p><a href="profile.php">Log in</a> to post a comment.</p>
            <?php endif; ?>

            <?php if ($comments): ?>
                <ul class="list-unstyled">
                    <?php foreach ($comments as $comment): ?>
                        <li class="media my-3 p-3 rounded" style="background-color: var(--bg-elevated); box-shadow: var(--card-shadow);">
                            <img src="<?= htmlspecialchars($comment['profile_image']) ?>" alt="<?= htmlspecialchars($comment['name']) ?>" class="mr-3 rounded-circle" style="width: 50px; height: 50px;">
                            <div class="media-body">
                                <h5 class="mt-0 mb-1" style="color: var(--text-primary);"><?= htmlspecialchars($comment['name']) ?> <small style="color: var(--text-secondary);"><?= date("F j, Y, g:i a", strtotime($comment['time'])) ?></small></h5>
                                <p style="color: var(--text-primary);"><?= htmlspecialchars($comment['content']) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'ui/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="../assets/main.js"></script>
    <script src="../assets/js/builder.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ratingStars = document.getElementById('rating-stars');
            const stars = ratingStars.querySelectorAll('.star');
            const ratingForm = document.createElement('form');
            const ratingInput = document.createElement('input');
            
            const initialRating = [...stars].filter(star => star.classList.contains('fas')).length;

            ratingInput.type = 'hidden';
            ratingInput.name = 'rating';
            ratingForm.method = 'POST';
            ratingForm.appendChild(ratingInput);
            document.body.appendChild(ratingForm);

            const updateStars = (upTo, isHover = false) => {
                stars.forEach((star, index) => {
                    star.classList.toggle('fas', index <= upTo);
                    star.classList.toggle('fa', index > upTo);
                    if (isHover) star.classList.toggle('hover', index <= upTo);
                });
            };

            const resetToInitialRating = () => updateStars(initialRating - 1);

            stars.forEach(star => {
                const index = parseInt(star.getAttribute('data-index'));

                star.addEventListener('mouseenter', () => updateStars(index, true));
                star.addEventListener('click', () => {
                    ratingInput.value = index + 1;
                    ratingForm.submit();
                });
            });

            ratingStars.addEventListener('mouseleave', () => {
                resetToInitialRating();
                stars.forEach(star => star.classList.remove('hover'));
            });

            resetToInitialRating();
        });
    </script>
</body>
</html>