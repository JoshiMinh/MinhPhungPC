include '../core/helpers.php';

if (empty($active) || !$active) {
    header("Location: dash.php");
    exit();
}

$product_id = $_GET['product_id'] ?? '';
$view = $_GET['view'] ?? '';
$addAction = !$product_id;

$product = [];
$specs = [];

if (!$addAction) {
    $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $specs = json_decode($product['specs'], true) ?: [];
    }
}

// Fetch all brands for dropdown
$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$_POST['product_id']]);
        header("Location: index.php?view=manage_products");
        exit();
    }

    $name = $_POST['name'] ?? '';
    $price = (float)str_replace(',', '', $_POST['price'] ?? '0');
    $brand_id = $_POST['brand_id'] ?? '';
    $image = $_POST['image'] ?? '';
    $type = $_POST['type'] ?? '';

    // Collect specs from POST
    $updatedSpecs = [];
    if (isset($_POST['spec_keys']) && isset($_POST['spec_values'])) {
        foreach ($_POST['spec_keys'] as $index => $key) {
            if (!empty($key)) {
                $updatedSpecs[$key] = $_POST['spec_values'][$index] ?? '';
            }
        }
    }
    $specsJson = json_encode($updatedSpecs);

    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, brand_id, image, type, specs, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $price, $brand_id, $image, $type, $specsJson]);
        $newProductId = $pdo->lastInsertId();
        header("Location: index.php?view=$view&product_id=$newProductId");
        exit();
    }

    if (isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, brand_id = ?, image = ?, type = ?, specs = ? WHERE product_id = ?");
        $stmt->execute([$name, $price, $brand_id, $image, $type, $specsJson, $product_id]);
        header("Location: index.php?view=$view&product_id=$product_id");
        exit();
    }
}
?>
<style>
    .form-control {
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .form-control:focus {
        background: var(--bg-secondary);
        color: var(--text-primary);
    }
</style>

<div class="container text-light my-4">
    <h3 class="text-center"><?= $addAction ? 'Add Product' : 'Edit Product' ?></h3>

    <form method="POST" class="col-md-9 mx-auto">
        <?php if (!$addAction): ?>
            <div class="mb-3 text-center">
                <img src="<?= $product['image'] ?? '' ?>" alt="Product Image" class="img-fluid" style="max-width: 450px;">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price (VNĐ)</label>
            <input 
                type="text" 
                id="price" 
                name="price" 
                class="form-control" 
                value="<?= isset($product['price']) ? number_format($product['price']) : '' ?>" 
                oninput="formatPrice(this)" 
                required
            >
        </div>
        <div class="mb-3">
            <label for="brand_id" class="form-label">Brand</label>
            <select name="brand_id" id="brand_id" class="form-control" required>
                <option value="">Select Brand</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b['brand_id'] ?>" <?= (isset($product['brand_id']) && $product['brand_id'] == $b['brand_id']) ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Product Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="">Select Type</option>
                <?php foreach (getProductTypeMapping() as $typeKey => $typeName): ?>
                    <option value="<?= $typeKey ?>" <?= (isset($product['type']) && $product['type'] == $typeKey) ? 'selected' : '' ?>><?= htmlspecialchars($typeName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image (URL)</label>
            <input type="text" id="image" name="image" class="form-control" value="<?= htmlspecialchars($product['image'] ?? '') ?>" required>
        </div>

        <hr>
        <h5>Specifications</h5>
        <div id="specs-container">
            <?php foreach ($specs as $key => $value): ?>
                <div class="row mb-2 spec-row">
                    <div class="col-5">
                        <input type="text" name="spec_keys[]" class="form-control" placeholder="Key (e.g. socket_type)" value="<?= htmlspecialchars($key) ?>">
                    </div>
                    <div class="col-6">
                        <input type="text" name="spec_values[]" class="form-control" placeholder="Value" value="<?= htmlspecialchars($value) ?>">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger btn-sm remove-spec">&times;</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-secondary btn-sm mb-3" id="add-spec">Add Specification</button>

        <div class="mb-3 text-center">
            <?php if (!$addAction): ?>
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirmDelete()">Delete</button>
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?? '' ?>">
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-success">Add Product</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    document.getElementById('add-spec').addEventListener('click', function() {
        const container = document.getElementById('specs-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 spec-row';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" name="spec_keys[]" class="form-control" placeholder="Key">
            </div>
            <div class="col-6">
                <input type="text" name="spec_values[]" class="form-control" placeholder="Value">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger btn-sm remove-spec">&times;</button>
            </div>
        `;
        container.appendChild(row);
    });

    document.getElementById('specs-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-spec')) {
            e.target.closest('.spec-row').remove();
        }
    });

    function confirmDelete() {
        return confirm('Are you sure you want to delete this product?');
    }

    function formatPrice(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function stripFormatting(input) {
        input.value = input.value.replace(/,/g, '');
    }

    window.onload = function() {
        const priceField = document.getElementById('price');
        if (priceField) {
            formatPrice(priceField);
            priceField.addEventListener('blur', () => formatPrice(priceField));
            priceField.form.addEventListener('submit', () => stripFormatting(priceField));
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>