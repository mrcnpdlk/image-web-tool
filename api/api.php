<?php
/**
 * Image Web Tool
 *
 * Copyright (c) 2018 http://pudelek.org.pl
 *
 * @license MIT License (MIT)
 *
 * For the full copyright and license information, please view source file
 * that is bundled with this package in the file LICENSE
 *
 * @author  Marcin Pudełek <marcin@pudelek.org.pl>
 */

use mrcnpdlk\ImageWebTool\Bootstrap;
use mrcnpdlk\ImageWebTool\FileHandler;
use mrcnpdlk\ImageWebTool\Helper;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

/**
 * API Configuration
 */

$app = new App(
    [
        'settings' => [
            'displayErrorDetails' => Helper::getConfig('debug', false),
        ],
    ]
);


$app->get('/{params}[/{file}]', function (Request $request, Response $response, $args) {

    $oBootstrap = new Bootstrap($request, $args);

    /**
     * @var string                         $imageBlob
     * @var \mrcnpdlk\Lib\PfcAdapter\Cache $oCache
     */
    $oCache    = Helper::getConfig('cacheInstance');
    $imageBlob = $oCache
        ->set(
            function () use ($oBootstrap) {
                $oFile = new FileHandler($oBootstrap);

                return $oFile->getBlob();
            },
            $oBootstrap->getHash(),
            null,
            $oBootstrap->isFileExists() ? 3600 * 24 : 0 // 24h
        )
        ->get()
    ;

    $response->write($imageBlob);

    return $response->withHeader('Content-Type', Helper::getMimeFromBlob($imageBlob));

});

$app->run();
