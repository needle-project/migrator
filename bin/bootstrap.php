<?php
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Symfony\Component\Console\Application;
use NeedleProject\Migrator\Command\MigrateCommand;

$application = new Application();
$application->add(new MigrateCommand());
$application->run();
