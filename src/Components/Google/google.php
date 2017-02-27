#!/usr/bin/env php
<?php

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

require __DIR__ . '/../../../vendor/autoload.php';
use Aigisu\Components\Google\GoogleDriveFilesystem;
use Aigisu\Core\Configuration;

/** @var $google GoogleDriveFilesystem */
$google = (new Configuration())->get(GoogleDriveFilesystem::class);
$clientManager = $google->getClientManager();

try {
    echo 'Access...' . PHP_EOL;
    $clientManager->setAccess();
    echo '[DONE]' . PHP_EOL;
} catch (RuntimeException $e) {
    $client = $clientManager->getClient();
    if (!$client->isUsingApplicationDefaultCredentials()) {
        echo 'You need to verify access.' . PHP_EOL .
            'Go to this page and type the code.' . PHP_EOL .
            "URL:\t" . $client->createAuthUrl() . PHP_EOL .
            "CODE:\t";
        $code = trim(fgets(STDIN));
        echo PHP_EOL . 'Authenticate...' . PHP_EOL;
        $response = $client->fetchAccessTokenWithAuthCode(trim($code));
    } else {
        echo PHP_EOL . 'Authenticate...' . PHP_EOL;
        $response = $client->fetchAccessTokenWithAssertion();
    }

    if (isset($response['error'])) {
        echo "[ERROR]: {$response['error']} - {$response['error_description']}" . PHP_EOL;
    } else {
        echo '[DONE]' . PHP_EOL .
            'Saving token to file...' . PHP_EOL;
        $clientManager->saveAccessToken();
        echo '[DONE]' . PHP_EOL .
            '[Got access]' . PHP_EOL;
    }
}
