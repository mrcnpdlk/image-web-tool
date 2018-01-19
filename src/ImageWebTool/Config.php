<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 23:10
 */

namespace mrcnpdlk\ImageWebTool;


use Noodlehaus\AbstractConfig;

/**
 * Class Config
 *
 * @package mrcnpdlk\ImageWebTool
 */
class Config extends AbstractConfig
{
    protected function getDefaults(): array
    {
        return [
            'storage'       => \dirname(__DIR__, 2) . '/content',
            'font'          => \dirname(__DIR__, 2) . '/fonts/blowbrush/blowbrush.ttf',
            'cacheInstance' => null,
            'debug'         => false,
        ];
    }

}
