<?php include 'scripts/categoryMap.php'; ?>

<style>
.navbar-main, .navbar-secondary, .navbar-mobile, .navbar-mobile-secondary {
    transition: background-color 0.3s, border-color 0.3s;
}

.navbar-main {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
}

.navbar-secondary {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
}

.navbar-secondary a, .navbar-mobile-secondary a {
    color: white;
    text-align: center;
    padding: 10px;
    display: block;
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
    transition: background-color 0.3s;
}

.search-button:hover {
    background-color: var(--primary-hover);
}

.dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
    position: absolute;
    z-index: 1000;
}

.navbar-toggler {
    border-color: transparent;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba%2888, 88, 88, 1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
}

.navbar-mobile {
    background-color: var(--bg-primary);
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    z-index: 1050;
    transition: max-height 0.3s ease-in-out;
    overflow: hidden;
}

.navbar-mobile.open {
    max-height: 500px;
    border-bottom: 1px solid var(--border-color);
}

.navbar-mobile.closed {
    max-height: 0;
    border-bottom: none;
}

.navbar-mobile-secondary {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
}
</style>

<nav>
    <nav class="navbar navbar-expand-lg navbar-main d-none d-lg-flex">
        <a href="index.php" class="navbar-brand" style="width: 10%;" title="Home">
            <img src="logo.png" alt="MinhPhungPC Logo" style="width: 100%;">
        </a>
        <div class="mx-auto" style="width: 50%;">
            <div class="search-input-container">
                <input type="text" class="search-input" id="searchQuery" placeholder="Find component" onkeypress="checkEnter(event)" onkeyup="debouncedSearchComponents()">
                <button class="search-button" id="searchBtn" onclick="performSearch()">
                    <i class="fas fa-search"></i>
                </button>
                <div id="searchDropdown" class="dropdown-menu w-100 bg-dark text-light p-0"></div>
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
                        <img src="<?= htmlspecialchars($_SESSION['profile_image']); ?>" class="rounded-circle" style="width: 35px; height: 35px;" title="Profile">
                    </a>
                </div>
                <a href="cart.php" class="btn btn-secondary" title="View Cart">
                    <i class="fas fa-shopping-cart"></i> <?= $cart_amount; ?>
                </a>
            <?php else: ?>
                <div class="mx-2">
                    <a href="account.php" class="btn btn-primary" title="Sign In">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
                <a href="account.php" class="btn btn-secondary" title="View Cart">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <nav class="navbar navbar-secondary navbar-gradient d-none d-lg-flex">
        <div class="navbar-collapse">
            <ul class="navbar-nav flex-row w-100">
                <?php foreach ($categoryMap as $category => $tableName): ?>
                    <li class="nav-item flex-fill">
                        <a href="categories.php?table=<?= urlencode($tableName); ?>" class="nav-link" title="Explore <?= htmlspecialchars($category); ?>"><?= htmlspecialchars($category); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <div class="d-lg-none">
        <div class="d-flex align-items-center p-2">
            <a href="index.php" class="navbar-brand" style="width: 30%;" title="Home">
                <img src="logo_light.png" alt="MinhPhungPC Logo" style="width: 100%;">
            </a>
            <button class="btn btn-link ml-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavbar" aria-controls="mobileNavbar">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNavbar" aria-labelledby="offcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="search-input-container p-2">
                    <input type="text" class="search-input" id="mobileSearchQuery" placeholder="Find component" onkeypress="checkEnter(event)" onkeyup="debouncedSearchComponents()">
                    <button class="search-button" onclick="performSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="navbar-mobile-secondary">
                    <?php foreach ($categoryMap as $category => $tableName): ?>
                        <a href="categories.php?table=<?= urlencode($tableName); ?>" class="nav-link" title="Explore <?= htmlspecialchars($category); ?>"><?= htmlspecialchars($category); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const performSearch = () => {
    const query = document.getElementById('searchQuery').value;
    if (query) window.location.href = 'search_result.php?query=' + encodeURIComponent(query);
};

const checkEnter = (event) => event.key === 'Enter' && performSearch();

let debounceTimer;
function debouncedSearchComponents() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(searchComponents, 100);
}

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
                let output = components.length === 0 
                    ? "<button class='dropdown-item' disabled>No results found</button>" 
                    : components.map(component => ` 
                        <div class='dropdown-item' onclick='window.location.href="item.php?table=${encodeURIComponent(component.item_table)}&id=${encodeURIComponent(component.id)}"' style='display: flex; align-items: center;'>
                            <img src='${component.image}' alt='${component.name}' style='width: 60px; height: 60px; margin-right: 10px;'>
                            <div style='display: flex; flex-direction: column;'>
                                <strong>${component.name}</strong>
                                <span>${new Intl.NumberFormat('vi-VN').format(component.price)}₫</span>
                                <span>${component.brand}</span>
                            </div>
                        </div>`).join('');
                searchDropdown.innerHTML = output;
            } else {
                searchDropdown.innerHTML = "<button class='dropdown-item' disabled>Error loading results</button>";
            }
        };

        xhr.onerror = () => searchDropdown.innerHTML = "<button class='dropdown-item' disabled>Error loading results</button>";
        xhr.send();
    } else {
        searchDropdown.style.display = 'none';
    }
}

document.addEventListener('click', (event) => {
    if (!document.getElementById('searchQuery').contains(event.target) && !document.getElementById('searchDropdown').contains(event.target)) {
        document.getElementById('searchDropdown').style.display = 'none';
    }
});
</script>