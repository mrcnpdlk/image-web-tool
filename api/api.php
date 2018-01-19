<?php

use mrcnpdlk\ImageWebTool\Bootstrap;
use mrcnpdlk\ImageWebTool\Config;
use mrcnpdlk\ImageWebTool\FileHandler;
use mrcnpdlk\ImageWebTool\Helper;
use phpFastCache\Helper\Psr16Adapter;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

/**
 * API Configuration
 */
Helper::setConfig(
    new Config([
            'storage'       => __DIR__ . '/../content',
            'font'          => __DIR__ . '/../fonts/blowbrush/blowbrush.ttf',
            'cacheInstance' => new Psr16Adapter(
                'redis',
                [
                    "host"                => null, // default localhost
                    "port"                => null, // default 6379
                    'defaultTtl'          => 3600 * 24, // 24h
                    'ignoreSymfonyNotice' => true,
                ]),
            'debug'         => true,
        ]
    )
);


$app = new App(
    [
        'settings' => [
            'displayErrorDetails' => Helper::getConfig()->get('debug', false),
        ],
    ]
);


$app->get('/{version}/{params}[/{file}]', function (Request $request, Response $response, $args) {

    $oBootstrap = new Bootstrap($request, $args);
    /**
     * @var string $imageBlob
     */
    $imageBlob = Helper::getFromCache(
        Helper::getConfig()->get('cacheInstance'),
        function () use ($oBootstrap) {
            $oFile = new FileHandler($oBootstrap->getFileName(), $oBootstrap->getParams());

            return $oFile->getBlob();
        },
        $oBootstrap->getHash(),
        3600 * 24 // 24h cache
    );
    $response->write($imageBlob);

    return $response->withHeader('Content-Type', Helper::getMimeFromBlob($imageBlob));

});

$app->run();
