<?php

use mrcnpdlk\ImageWebTool\Bootstrap;
use mrcnpdlk\ImageWebTool\FileHandler;
use mrcnpdlk\ImageWebTool\Helper;
use phpFastCache\Helper\Psr16Adapter;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

$oInstanceCacheRedis = new Psr16Adapter(
    'redis',
    [
        "host"                => null, // default localhost
        "port"                => null, // default 6379
        'defaultTtl'          => 3600 * 24, // 24h
        'ignoreSymfonyNotice' => true,
    ]);

$config = [
    'settings' => [
        'displayErrorDetails' => false,
    ],
];

$app = new App($config);

// cache injection
$container                  = $app->getContainer();
$container['cacheInstance'] = $oInstanceCacheRedis;


$app->get('/{version}/{params}[/{file}]', function (Request $request, Response $response, $args) {

    $oBootstrap = new Bootstrap($request, $args);
    /**
     * @var string $imageBlob
     */
    $imageBlob = Helper::getFromCache(
        $this->cacheInstance,
        function () use ($oBootstrap) {
            $oFile = new FileHandler($oBootstrap->getFileName(), $oBootstrap->getParams());

            return $oFile->getBlob();
        },
        $oBootstrap->getHash(),
        3600 * 24
    );
    $response->write($imageBlob);

    return $response->withHeader('Content-Type', Helper::getMimeFromBlob($imageBlob));

});

$app->run();
