<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 01:26
 */
use Lcobucci\JWT\Signer\Key;

return [
    'public' => new Key('file://' . __DIR__ . '/auth/public.key'),
    'private' => new Key('file://' . __DIR__ . '/auth/private.key'),
];
