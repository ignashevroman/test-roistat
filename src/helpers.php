<?php

if (!function_exists('storage_path')) {
    function storage_path(string $path = '') {
        return __DIR__ . '/../storage/' . $path;
    }
}

if (!function_exists('root_path')) {
    function root_path(string $path = '') {
        return __DIR__ . '/../' . $path;
    }
}

if (!function_exists('templates_path')) {
    function templates_path(string $path = '') {
        return __DIR__ . '/../resources/templates/' . $path;
    }
}
