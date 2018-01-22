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
            'debug'         => false,
            'font_size'     => 50,
        ];
    }

}
