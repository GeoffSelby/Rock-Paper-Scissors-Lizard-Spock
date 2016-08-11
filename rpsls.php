<?php

require __DIR__ . '/vendor/autoload.php';

use App\PlayGameCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$input = new ArrayInput(['play']);
$application = new Application;
$application->add(new PlayGameCommand);
$application->run($input);
