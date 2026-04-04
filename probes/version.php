<?php
echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
?>
