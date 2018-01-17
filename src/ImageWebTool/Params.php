<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 23:29
 */

namespace mrcnpdlk\ImageWebTool;

/**
 * Class Params
 *
 * @package mrcnpdlk\ImageWebTool
 */
class Params
{
    const W_SCALE = 'scale';
    const W_FIT   = 'fit';
    const W_FILL  = 'fill';

    /**
     * Width (px) - if NULL original value is taken
     *
     * @var int|null
     */
    public $w;
    /**
     * Height (px)  - if NULL original value is taken
     *
     * @var int|null
     */
    public $h;
    /**
     * Crop mode
     *
     * SCALE - Change the size of the image exactly to the given width and height without necessarily retaining the original aspect ratio:
     * all original image parts are visible but might be stretched or shrunk
     *
     * FIT - The image is resized so that it takes up as much space
     * as possible within a bounding box defined by the given width and height parameters. The original aspect ratio is retained and all of
     * the original image is visible
     *
     * FILL  - Create an image with the exact given width and height while retaining the original aspect ratio, using only part of the
     * image that fills the given dimensions if necessary (only part of the original image might be visible if the requested aspect ratio
     * is different from the original aspect ratio)
     *
     * @var string|null
     */
    public $c;
    /**
     * Quality 0-100
     *
     * @var integer
     */
    public $q;

    /**
     * Params constructor.
     *
     * @param string $params
     */
    public function __construct(string $params = null)
    {
        $params = trim($params);
        if (!empty($params)) {
            $tParams = explode(',', $params);
            foreach ($tParams as $param) {
                $tKeyValuePair = explode('_', $param);
                if (\count($tKeyValuePair) === 2) {
                    $this->parseKeyValuePair($tKeyValuePair[0], $tKeyValuePair[1]);
                }
            }
        }
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    protected function parseKeyValuePair(string $key, $value)
    {
        $setter = 'set' . $this->getCamelCaseName($key);
        if (method_exists($this, $setter)) {
            $this->{$setter}($value);
        } elseif (property_exists($this, $key)) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function getCamelCaseName($name): string
    {
        return str_replace(
            ' ', '', ucwords(str_replace(['_', '-'], ' ', $name))
        );
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setC($value)
    {
        $this->c = strtolower((string)$value);

        return $this;
    }

    /**
     * Height
     *
     * @param $value
     *
     * @return $this
     */
    public function setH($value)
    {
        $this->h = (int)$value;

        return $this;
    }

    /**
     * Width
     *
     * @param $value
     *
     * @return $this
     */
    public function setW($value)
    {
        $this->w = (int)$value;

        return $this;
    }

    /**
     * @return $this
     */
    public function standardize()
    {
        if (null === $this->c) {
            if ($this->w && $this->h) {
                $this->c = self::W_SCALE;
            } else {
                $this->c = self::W_FIT;
            }
        }

        if ($this->q <= 0 || $this->q > 100 || null === $this->q) {
            $this->q = 75;
        }

        return $this;
    }

    /**
     * @param string $format
     *
     * @return array
     */
    public function getQuality(string $format = 'jpg'): array
    {
        $answer = [];
        switch (strtolower($format)) {
            case 'jpg':
            case 'jpeg':
                $q = ['jpeg_quality' => $this->q];
                break;
            case 'png':
                break;
            default:
                break;
        }

        return array_merge($q) ?? [];
    }
}
