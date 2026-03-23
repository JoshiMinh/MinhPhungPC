<?php
// Unified product schema metadata
$categoryMap = [
    "CPU"              => "cpu",
    "Graphics Card"    => "gpu",
    "RAM"              => "ram",
    "Motherboard"      => "motherboard",
    "Storage"          => "storage",
    "Power Supply"     => "psu",
    "Case"             => "case",
    "Cooler"           => "cooler",
    "Operating System" => "os",
    "Fan"              => "fan",
];

// Specification fields for different product types (stored in specs JSONB)
$specs_metadata = [
    'cpu'         => ['core_count', 'thread_count', 'socket_type', 'TDP'],
    'motherboard' => ['socket_type', 'chipset', 'memory_slots', 'max_memory_capacity', 'ddr', 'expansion_slots'],
    'ram'         => ['ddr', 'capacity', 'speed'],
    'gpu'         => ['vram_capacity', 'cuda_cores', 'TDP'],
    'storage'     => ['type', 'capacity', 'speed', 'port'],
    'cooler'      => ['cooling_type', 'socket'],
    'psu'         => ['wattage', 'efficiency_rating'],
    'case'        => ['size'],
    'os'          => ['version'],
];
?>
