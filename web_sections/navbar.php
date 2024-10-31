<nav class="navbar navbar-expand-lg navbar-main">
    <a class="navbar-brand" href="index.php" style="width: 10%;">
        <img src="logo.png" alt="MinhPhungPC Logo" style="width: 100%;">
    </a>
    <div class="mx-auto" style="width: 50%;">
        <div class="search-input-container d-flex">
            <input class="search-input flex-grow-1" type="text" placeholder="Find component" aria-label="Search">
            <button class="search-button" aria-label="Search"><i class="fas fa-search"></i></button>
        </div>
    </div>
    <div class="d-flex align-items-center ml-auto">
        <button id="switchBtn" class="btn btn-link p-0 ml-2" aria-label="Toggle dark mode">
            <i class="bi bi-moon icon"></i>
        </button>
        <button class="btn btn-primary mx-2"><i class="fas fa-user"></i> Account</button>
        <button class="btn btn-secondary"><i class="fas fa-shopping-cart"></i> Cart</button>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-secondary navbar-gradient">
    <ul class="navbar-nav flex-row w-100">
        <?php foreach (["Motherboard", "CPU", "RAM", "Graphics Card", "Storage", "Power Supply", "Cooler", "Case Fan", "Operating System"] as $category): ?>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="categories.php?category=<?= urlencode($category); ?>">
                    <?= htmlspecialchars($category); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>