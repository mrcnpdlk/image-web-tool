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
    private $filePath;

    /**
     * Bootstrap constructor.
     *
     * @param \Slim\Http\Request $oRequest
     * @param array              $args
     */
    public function __construct(Request $oRequest, array $args)
    {
        $this->hash     = md5(__DIR__ . $oRequest->getUri()->getPath());
        $params         = isset($args['file']) ? $args['params'] : null;
        $this->filePath = str_replace(':', '/', $args['file'] ?? $args['params']);

        $this->oParams = new Params($params);
        $this->oParams->standardize();
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

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return pathinfo(basename($this->getFilePath()), \PATHINFO_FILENAME);
    }

    /**
     * @return mixed|string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return bool
     */
    public function isFileExists()
    {
        return is_file($this->getFileRealpath()) && file_exists($this->getFileRealpath()) && is_readable($this->getFileRealpath());
    }

    /**
     * @return string
     */
    public function getFileRealpath()
    {
        //* security protection *//
        $fileName = str_replace('..:', '', $this->getFilePath());

        return rtrim(Helper::getConfig('storage', ''), '/') . \DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return pathinfo(basename($this->getFilePath()), \PATHINFO_EXTENSION);
    }
}
