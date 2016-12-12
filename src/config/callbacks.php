<?php

/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-14
 * Time: 18:46
 */

return [
    'spriteGenerator' => function (\Interop\Container\ContainerInterface $container) {
        $generator = 'php ' . $container->get('root') . '/bin/css-sprite-generator/cli.php ';
        $params = [
            $container->get('public') . '/icons',
            $container->get('sprite.icons'),
            $container->get(\Aigisu\Components\Url\UrlManager::class)->to('storage.icons.sprite'),
        ];

        exec($generator . escapeshellcmd(implode(' ', $params)) . ' > /dev/null 2>/dev/null &');
    },
];
