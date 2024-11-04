<?php
$components = [
    'cpucooler' => ['id', 'name', 'brand', 'price', 'image', 'cooling_type', 'fan_size', 'tdp_support'],
    'graphicscard' => ['id', 'name', 'brand', 'price', 'image', 'vram_capacity', 'core_clock_speed', 'boost_clock_speed', 'cuda_cores', 'tdp', 'interface'],
    'memory' => ['id', 'name', 'brand', 'price', 'image', 'type', 'capacity', 'speed'],
    'motherboard' => ['id', 'name', 'brand', 'price', 'image', 'socket_type', 'chipset', 'cpu', 'memory_slots', 'max_memory_capacity', 'ddr', 'expansion_slots'],
    'operatingsystem' => ['id', 'name', 'brand', 'price', 'image', 'version', 'license_type'],
    'pccase' => ['id', 'name', 'brand', 'price', 'image', 'form_factor_support', 'drive_bays', 'fan_mounts', 'radiator_support'],
    'powersupply' => ['id', 'name', 'brand', 'price', 'image', 'wattage', 'efficiency_rating', 'modular'],
    'processor' => ['id', 'name', 'brand', 'price', 'image', 'core_count', 'thread_count', 'base_clock_speed', 'boost_clock_speed', 'socket_type', 'tdp', 'integrated_graphics'],
    'storage' => ['id', 'name', 'brand', 'price', 'image', 'type', 'capacity', 'read_speed', 'write_speed', 'interface'],
];
?>