<?php
/**
 * Render a single component card for the PC builder.
 * Expects $tableName, $componentName, and $buildSetComponents to be defined.
 */
$selectedComponent = null;
if (isset($buildSetComponents)) {
    foreach ($buildSetComponents as $component) {
        if ($component['table'] === $tableName) {
            $selectedComponent = $component;
            break;
        }
    }
}
?>

<div class="component-card my-4 shadow-sm bg-white text-dark rounded" id="<?= htmlspecialchars($tableName); ?>"> 
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
        <span class="text-center" style="flex-shrink: 0; width: 120px;">
            <?= htmlspecialchars($componentName); ?>
        </span>

        <div class="d-flex align-items-center gap-3 w-100 w-md-auto">
            <div class="p-2">
                <img src="<?= $selectedComponent ? htmlspecialchars($selectedComponent['image']) : '../storage/images/' . htmlspecialchars($tableName . '.png'); ?>"
                    alt="<?= htmlspecialchars($componentName); ?>"
                    id="componentImage-<?= htmlspecialchars($tableName); ?>"
                    class="component-image"
                    style="background-color: #ffffff; opacity: 0.7; transition: opacity 0.3s ease; width: 50px; height: 50px; padding: 10px; border-radius: 5px;">
            </div>
            <span class="text-muted d-md-inline">
                <?= $selectedComponent ? "<span class='text-dark'>" . htmlspecialchars($selectedComponent['name']) . "</span><br> - <span class='text-success'>" . number_format($selectedComponent['price']) . "₫</span>" : "Please select a component"; ?>
            </span>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-center mt-2 mt-md-0">
            <?php if ($selectedComponent): ?>
                <div class="d-flex gap-2">
                    <form method="post" action="core/remove.php" style="display: inline;">
                        <input type="hidden" name="table" value="<?= htmlspecialchars($tableName); ?>">
                        <button type="submit" class="btn btn-danger px-2 mt-2 mt-md-0 mx-1">Remove</button>
                    </form>
                    <button class="btn btn-primary px-3 select-btn mt-2 mt-md-0 mx-1"
                        onclick="modalFetchItems('<?= htmlspecialchars($componentName); ?>', '<?= htmlspecialchars($tableName); ?>')"
                        data-toggle="modal" data-target="#componentModal">Change</button>
                </div>
            <?php else: ?>
                <button class="btn btn-primary px-4 select-btn mx-1"
                    onclick="modalFetchItems('<?= htmlspecialchars($componentName); ?>', '<?= htmlspecialchars($tableName); ?>')"
                    data-toggle="modal" data-target="#componentModal">Select</button>
            <?php endif; ?>
        </div>
    </div>
</div>
