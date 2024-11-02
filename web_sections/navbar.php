<nav class="navbar navbar-expand-lg navbar-main">
    <a href="index.php" class="navbar-brand" style="width: 10%;">
        <img src="logo.png" alt="MinhPhungPC Logo" style="width: 100%;">
    </a>
    <div class="mx-auto" style="width: 50%;">
        <div class="search-input-container d-flex">
            <input type="text" class="search-input flex-grow-1" id="searchQuery" placeholder="Find component" aria-label="Search" onkeypress="checkEnter(event)">
            <button class="search-button" id="searchBtn" aria-label="Search" onclick="performSearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="d-flex align-items-center ml-auto">
        <button id="switchBtn" class="btn btn-link p-0 ml-2" aria-label="Toggle dark mode">
            <i class="bi bi-moon icon"></i>
        </button>
        <a href="account.php" class="btn btn-primary mx-2">
            <i class="fas fa-user"></i> Account
        </a>
        <button class="btn btn-secondary">
            <i class="fas fa-shopping-cart"></i> Cart
        </button>
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
    if (query) {
        window.location.href = 'search_result.php?query=' + encodeURIComponent(query);
    }
}

function checkEnter(event) {
    if (event.key === 'Enter') performSearch();
}
</script>