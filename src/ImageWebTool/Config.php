<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 23:10
 */

namespace mrcnpdlk\ImageWebTool;


use mrcnpdlk\Lib\PfcAdapter\Cache;
use phpFastCache\CacheManager;

/**
 * Class Config
 *
 * @package mrcnpdlk\ImageWebTool
 */
class Config extends \Noodlehaus\Config
{
    /**
     * @return array
     */
    protected function getDefaults(): array
    {
        $config = [
            'host'                => null,
            'port'                => null,
            'defaultTtl'          => 3600 * 24, // 24h
            'ignoreSymfonyNotice' => true,
            'fallback'            => 'files',
        ];

        $oCache = new Cache(CacheManager::Redis($config));

        return [
            'storage'       => \dirname(__DIR__, 2) . '/content',
            'font'          => \dirname(__DIR__, 2) . '/fonts/blowbrush/blowbrush.ttf',
            'cacheInstance' => $oCache,
            'debug'         => true,
        ];
    }

}
