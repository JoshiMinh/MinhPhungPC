<nav class="navbar navbar-expand-lg navbar-main">
    <a href="index.php" class="navbar-brand" style="width: 10%;">
        <img src="logo.png" alt="MinhPhungPC Logo" style="width: 100%;">
    </a>
    <div class="mx-auto" style="width: 50%;">
        <div class="search-input-container d-flex">
            <input type="text" class="search-input flex-grow-1" id="searchQuery" placeholder="Find component" onkeypress="checkEnter(event)">
            <button class="search-button" id="searchBtn" onclick="performSearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="d-flex align-items-center ml-auto">
        <button id="switchBtn" class="btn btn-link p-0" aria-label="Toggle dark mode">
            <i class="bi bi-moon icon"></i>
        </button>
        
        <?php session_start(); ?>
        <div class="mx-2">
            <?php if (isset($_SESSION['profile_image'])): ?>
                <a href="account.php">
                    <img src="<?= htmlspecialchars($_SESSION['profile_image']); ?>" style="width: 35px; height: 35px; border-radius: 50%;">
                </a>
            <?php else: ?>
                <a href="account.php" class="btn btn-primary">
                    <i class="fas fa-user"></i> Account
                </a>
            <?php endif; ?>
        </div>
        
        <a href="<?= isset($_SESSION['user_id']) ? 'cart.php' : 'account.php'; ?>" class="btn btn-secondary">
            <i class="fas fa-shopping-cart"></i>
        </a>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-secondary navbar-gradient">
    <ul class="navbar-nav flex-row w-100">
        <?php include 'categoryMap.php'; ?>
        <?php foreach ($categoryMap as $category => $tableName): ?>
            <li class="nav-item flex-fill">
                <a href="categories.php?table=<?= urlencode($tableName); ?>" class="nav-link"><?= htmlspecialchars($category); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<script>
function performSearch() {
    const query = document.getElementById('searchQuery').value;
    if (query) window.location.href = 'search_result.php?query=' + encodeURIComponent(query);
}

function checkEnter(event) {
    if (event.key === 'Enter') performSearch();
}
</script>