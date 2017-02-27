#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-03
 * Time: 14:05
 */
require __DIR__ . '/../../../vendor/autoload.php';

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

use Aigisu\Components\Imgur\Client;
use GuzzleHttp\Psr7\Uri;
use function GuzzleHttp\Psr7\parse_query;

/** @var $client Client */
$client = (new \Aigisu\Core\Configuration())->get(\Aigisu\Components\Imgur\Imgur::class)->getClient();

echo $client->getAuthorization()->getAuthorizationUrl() . PHP_EOL;
$uri = new Uri(trim(fgets(STDIN)));
$query = parse_query($uri->getQuery());
if (!isset($query['code'])) {
    throw new InvalidArgumentException('Missing code in url');
}

$client->fetchAccessTokenWithAuthCode($query['code']);

$client->saveAccessToken();
