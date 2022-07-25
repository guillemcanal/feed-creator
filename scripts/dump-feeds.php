<?php

use Gcanal\FeedCreator\Config;
use Gcanal\FeedCreator\FeedsDumper;

require dirname(__DIR__) . '/vendor/autoload.php';

$config = Config::fromJSONFile(dirname(__DIR__) . '/config.json');
$feedsDumper = new FeedsDumper($config);
$feedsDumper->dump();