<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$args = $_SERVER['argv'];
array_shift($args); // remove script name

// Run PHPUnit application directly
$application = new \PHPUnit\TextUI\Application();
exit($application->run(array_merge(['phpunit', '--configuration', __DIR__ . '/../phpunit.xml'], $args)));
