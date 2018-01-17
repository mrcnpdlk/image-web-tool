<?php
/**
 * Created by Marcin.
 * Date: 17.01.2018
 * Time: 23:32
 */

namespace mrcnpdlk\ImageWebTool;


use Slim\Http\Request;

class Bootstrap
{
    /**
     * @var string|null
     */
    private $ipAddress;
    /**
     * @var string
     */
    private $hash;
    /**
     * @var string
     */
    private $version;
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
        $this->ipAddress = $oRequest->getAttribute('ip_address', null);
        $this->hash      = md5($oRequest->getUri()->getPath());
        $this->version   = $args['version'];
        $params          = isset($args['file']) ? $args['params'] : null;
        $this->fileName  = basename($args['file'] ?? $args['params']);

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
     * @return null|string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @return \mrcnpdlk\ImageWebTool\Params
     */
    public function getParams(): Params
    {
        return $this->oParams;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
