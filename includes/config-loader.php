<?php
/**
 * CityDirectory — Configuration Loader
 * Loads config.php and provides access via config() helper.
 */

function config(?string $key = null, $default = null) {
    static $config = null;
    if ($config === null) {
        $path = __DIR__ . '/../config.php';
        if (!file_exists($path)) {
            die('Configuration file not found. Copy config.example.php to config.php');
        }
        $config = require $path;
    }
    
    global $_SITE_SETTINGS;
    
    if ($key === null) {
        return is_array($_SITE_SETTINGS) ? array_merge($config, $_SITE_SETTINGS) : $config;
    }
    
    if (isset($_SITE_SETTINGS) && is_array($_SITE_SETTINGS) && array_key_exists($key, $_SITE_SETTINGS)) {
        return $_SITE_SETTINGS[$key];
    }
    
    // Support dot notation: config('db_host')
    return $config[$key] ?? $default;
}

/**
 * Get the base URL for the site.
 */
function base_url(string $path = ''): string {
    return rtrim(config('base_url'), '/') . '/' . ltrim($path, '/');
}

/**
 * Get the asset URL with cache-busting.
 */
function asset(string $path): string {
    $file = __DIR__ . '/../' . ltrim($path, '/');
    $version = file_exists($file) ? filemtime($file) : time();
    return base_url($path) . '?v=' . $version;
}
