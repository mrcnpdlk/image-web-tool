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
 * @author Marcin Pudełek <marcin@pudelek.org.pl>
 */

namespace mrcnpdlk\ImageWebTool;


use Slim\Http\Request;

class Bootstrap
{
    /**
     * @var string
     */
    private $hash;
    /**
     * @var \mrcnpdlk\ImageWebTool\Params
     */
    private $oParams;
    /**
     * @var mixed string
     */
    private $fileName;

    /**
     * Bootstrap constructor.
     *
     * @param \Slim\Http\Request $oRequest
     * @param array              $args
     */
    public function __construct(Request $oRequest, array $args)
    {
        $this->hash     = md5($oRequest->getUri()->getPath());
        $params         = isset($args['file']) ? $args['params'] : null;
        $this->fileName = basename($args['file'] ?? $args['params']);

        $this->oParams = new Params($params);
        $this->oParams->standardize();
    }

    /**
     * @return mixed|string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return \mrcnpdlk\ImageWebTool\Params
     */
    public function getParams(): Params
    {
        return $this->oParams;
    }
}
