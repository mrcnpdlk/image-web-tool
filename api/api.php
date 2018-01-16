<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,

        'logger' => [
            'name'  => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path'  => '/var/log/nginx/iwt-error.log',
        ],
    ],
];
$app    = new App($config);
$app->add(new RKA\Middleware\IpAddress(true, ['127.0.0.1']));

$app->get('/{vendor}/{opts}[/{file}]', function (Request $request, Response $response, $args) {
    $ipAddress = $request->getAttribute('ip_address');

    $vendor = $args['vendor'];
    $opts   = isset($args['file']) ? $args['opts'] : null;
    $file   = $args['file'] ?? $args['opts'];

    $oFile = new \mrcnpdlk\ImageWebTool\FileHandler($file, $opts);
    $image = $oFile->getBlob();
    $finfo = new finfo(\FILEINFO_MIME_TYPE);
    $mime  = $finfo->buffer($image);
    $response->write($image);

    return $response->withHeader('Content-Type', $mime);

    //$t   = [$vendor, explode(',', $opts), $file];
    //$t[] = $ipAddress;
    //return $response->withJson($t);
});

$app->run();
