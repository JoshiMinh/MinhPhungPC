<?php
$components = [
    'processor' => [
        'id', 'name', 'brand', 'price', 'image', 
        'core_count', 'thread_count', 'base_clock_speed', 
        'boost_clock_speed', 'socket_type', 'tdp', 
        'integrated_graphics'
    ],
    'motherboard' => [
        'id', 'name', 'brand', 'price', 'image', 
        'socket_type', 'chipset', 'cpu', 
        'memory_slots', 'max_memory_capacity', 'ddr', 
        'expansion_slots'
    ],
    'memory' => [
        'id', 'name', 'brand', 'price', 'image', 
        'type', 'capacity', 'speed'
    ],
    'graphicscard' => [
        'id', 'name', 'brand', 'price', 'image', 
        'vram_capacity', 'core_clock_speed', 
        'boost_clock_speed', 'cuda_cores', 'tdp', 
        'interface'
    ],
    'storage' => [
        'id', 'name', 'brand', 'price', 'image', 
        'type', 'capacity', 'read_speed', 
        'write_speed', 'interface'
    ],
    'cpucooler' => [
        'id', 'name', 'brand', 'price', 'image', 
        'cooling_type', 'fan_size', 'tdp_support'
    ],
    'powersupply' => [
        'id', 'name', 'brand', 'price', 'image', 
        'wattage', 'efficiency_rating', 'modular'
    ],
    'pccase' => [
        'id', 'name', 'brand', 'price', 'image', 
        'form_factor_support', 'drive_bays', 
        'fan_mounts', 'radiator_support'
    ],
    'operatingsystem' => [
        'id', 'name', 'brand', 'price', 'image', 
        'version', 'license_type'
    ],
];
?>