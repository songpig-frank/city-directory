<?php
/**
 * PHP Version Checker
 */
header('Content-Type: text/plain');
echo "PHP Version: " . PHP_VERSION . "\n";

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    echo "WARNING: Server is running PHP " . PHP_VERSION . ". \n";
    echo "The recent update uses Union Types (array|string) which require PHP 8.0+.\n";
} else {
    echo "PHP Version is compatible (8.0+).\n";
}
