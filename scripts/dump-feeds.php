<?php

use Gcanal\FeedCreator\Config;
use Gcanal\FeedCreator\FeedsDumper;

require dirname(__DIR__) . '/vendor/autoload.php';

$config = dirname(__DIR__) . '/config.json';
if (!file_exists($config)) {
    printf("You must generate a config.json first.\n");
    exit(2);
}

$configData = json_decode(file_get_contents($config));
$validator = new JsonSchema\Validator();
$validator->validate(
    $configData,
    (object)['$ref' => 'file://' . dirname(__DIR__) . '/config.schema.json']
);
if (!$validator->isValid()) {
    printf("You configuration file does not validate against config.schema.json:\n");
    foreach ($validator->getErrors() as $error) {
        printf("[%s] %s\n", $error['property'], $error['message']);
    }
    exit(2);
}

$feedsDirectory = getenv('FEEDS_DIRECTORY');
if (!$feedsDirectory) {
    printf("You must provide the environment variable FEEDS_DIRECTORY\n");
    exit(2);
}

$baseURL = getenv('BASE_URL');
if (!$baseURL) {
    printf("You must provide the environment variable BASE_URL\n");
    exit(2);
}

$feedsDumper = new FeedsDumper(
    Config::fromJSONFile($config),
    $feedsDirectory,
    $baseURL,
);

$feedsDumper->dump();
