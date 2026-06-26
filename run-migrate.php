<?php
$output = shell_exec('php artisan migrate:fresh --seed --force 2>&1');
echo "OUTPUT:\n" . $output . "\n";
