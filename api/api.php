<?php

use mrcnpdlk\ImageWebTool\Bootstrap;
use mrcnpdlk\ImageWebTool\Config;
use mrcnpdlk\ImageWebTool\FileHandler;
use mrcnpdlk\ImageWebTool\Helper;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

/**
 * API Configuration
 */
Helper::setConfig(
    new Config([__DIR__.'/../config/imagewebtool.ini'])
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
     * @var string                         $imageBlob
     * @var \mrcnpdlk\Lib\PfcAdapter\Cache $oCache
     */
    $oCache    = Helper::getConfig()->get('cacheInstance');
    $imageBlob = $oCache
        ->set(
            function () use ($oBootstrap) {
                $oFile = new FileHandler(
                    $oBootstrap->getFileName(),
                    $oBootstrap->getParams());

                return $oFile->getBlob();
            },
            $oBootstrap->getHash(),
            null,
            1
        )
        ->get()
    ;

    $response->write($imageBlob);

    return $response->withHeader('Content-Type', Helper::getMimeFromBlob($imageBlob));

});

$app->run();
