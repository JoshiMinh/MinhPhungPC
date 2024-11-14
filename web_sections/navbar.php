<style>
.navbar-main {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.3s, border-color 0.3s;
}

.navbar-secondary {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
}

.navbar-secondary a {
    color: white;
    text-align: center;
    transition: background-color 0.3s;
}

.navbar-secondary a:hover {
    background-color: var(--navbar-hover);
}

.search-input-container {
    display: flex;
    width: 100%;
    position: relative;
}

.search-input {
    flex: 1;
    padding: 8px;
    border: 1px solid var(--border-color);
    background-color: var(--bg-secondary);
    color: var(--text-primary);
    border-radius: 4px 0 0 4px;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.search-button {
    background-color: var(--primary-color);
    border: 1px solid var(--primary-color);
    border-radius: 0 4px 4px 0;
    padding: 8px 12px;
    cursor: pointer;
    color: white;
    transition: background-color 0.3s, border-color 0.3s;
}

.search-button:hover {
    background-color: var(--primary-hover);
}
</style>

<nav class="navbar navbar-expand-lg navbar-main">
    <a href="index.php" class="navbar-brand" style="width: 10%;">
        <img src="logo.png" alt="MinhPhungPC Logo" style="width: 100%;">
    </a>
    <div class="mx-auto" style="width: 50%;">
        <div class="search-input-container d-flex">
            <input type="text" class="search-input flex-grow-1" id="searchQuery" placeholder="Find component" onkeypress="checkEnter(event)" onkeyup="searchComponents()">
            <button class="search-button" id="searchBtn" onclick="performSearch()">
                <i class="fas fa-search"></i>
            </button>
            <div id="searchDropdown" class="dropdown-menu w-100 bg-dark text-light p-0" aria-labelledby="searchQuery" style="max-height: 300px; overflow-y: auto; position: absolute; z-index: 1000;"></div>
        </div>
    </div>
    <div class="d-flex align-items-center ml-auto">
        <button id="switchBtn" class="btn btn-link p-0" aria-label="Toggle dark mode">
            <i class="bi bi-moon icon"></i>
        </button>
        <?php if (isset($_SESSION['user_id'])): 
            $stmt = $pdo->prepare("SELECT cart FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $cart_data = $stmt->fetchColumn();
            $cart_amount = $cart_data ? count(explode(' ', trim($cart_data))) : 0;
        ?>
            <div class="mx-2">
                <a href="account.php">
                    <img src="<?= htmlspecialchars($_SESSION['profile_image']); ?>" class="rounded-circle" style="width: 35px; height: 35px;">
                </a>
            </div>
            <a href="cart.php" class="btn btn-secondary">
                <i class="fas fa-shopping-cart"></i> <?= $cart_amount; ?>
            </a>
        <?php else: ?>
            <div class="mx-2">
                <a href="account.php" class="btn btn-primary">
                    <i class="fas fa-user"></i>
                </a>
            </div>
            <a href="account.php" class="btn btn-secondary">
                <i class="fas fa-shopping-cart"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-secondary navbar-gradient">
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav" aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse d-none d-lg-block">
        <ul class="navbar-nav flex-row w-100">
            <?php include 'scripts/categoryMap.php'; ?>
            <?php foreach ($categoryMap as $category => $tableName): ?>
                <li class="nav-item flex-fill">
                    <a href="categories.php?table=<?= urlencode($tableName); ?>" class="nav-link"><?= htmlspecialchars($category); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarNav" aria-labelledby="sidebarNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarNavLabel">Categories</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <?php include 'scripts/categoryMap.php'; ?>
                <?php foreach ($categoryMap as $category => $tableName): ?>
                    <li class="nav-item">
                        <a href="categories.php?table=<?= urlencode($tableName); ?>" class="nav-link"><?= htmlspecialchars($category); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const performSearch = () => {
    const query = document.getElementById('searchQuery').value;
    if (query) window.location.href = 'search_result.php?query=' + encodeURIComponent(query);
};

const checkEnter = (event) => event.key === 'Enter' && performSearch();

function searchComponents() {
    const searchQuery = document.getElementById("searchQuery").value;
    const searchDropdown = document.getElementById("searchDropdown");

    if (searchQuery.length > 0) {
        searchDropdown.style.display = 'block';
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "_search.php?search=" + encodeURIComponent(searchQuery), true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const components = JSON.parse(xhr.responseText);
                let output = '';

                if (components.length === 0) {
                    output += "<button class='dropdown-item' disabled>No results found</button>";
                } else {
                    components.forEach(function(component) {
                        const formattedPrice = new Intl.NumberFormat('vi-VN').format(component.price);
                        output += `
                            <div class='dropdown-item' onclick='window.location.href="item.php?table=${encodeURIComponent(component.item_table)}&id=${encodeURIComponent(component.id)}"' style='display: flex; align-items: center;'>
                                <img src='${component.image}' alt='${component.name}' style='width: 60px; height: 60px; margin-right: 10px;'>
                                <div style='display: flex; flex-direction: column;'>
                                    <strong>${component.name}</strong>
                                    <span>${formattedPrice}₫</span>
                                    <span>${component.brand}</span>
                                </div>
                            </div>`;
                    });
                }

                searchDropdown.innerHTML = output;
            }
        };

        xhr.send();
    } else {
        searchDropdown.style.display = 'none';
    }
}

document.addEventListener('click', function(event) {
    const searchQuery = document.getElementById('searchQuery');
    const searchDropdown = document.getElementById('searchDropdown');
    if (!searchQuery.contains(event.target) && !searchDropdown.contains(event.target)) {
        searchDropdown.style.display = 'none';
    }
});
</script>