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
    const C_SCALE      = 'scale';
    const C_FIT        = 'fit';
    const C_FIT_MARGIN = 'fit-margin';
    const C_FILL       = 'fill';

    const E_GAMMA     = 'g';
    const E_NEGATIVE  = 'n';
    const E_GRAYSCALE = 'gr';
    const E_COLORIZE  = 'c';
    const E_BLUR      = 'b';

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
     * FIT-MARGIN - Like FIT, but image is increased to required dimension by additional margins (u+down or left+right)
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
     * Rotate - angle in degrees
     *
     * @var integer
     */
    public $r;
    /**
     * Background color in HEX format
     *
     * @var string
     */
    public $bgc;
    /**
     * Effects
     *
     * @var string
     */
    public $e;
    /**
     * Effect option
     *
     * @see http://imagine.readthedocs.io/en/latest/usage/effects.html
     *
     * @var string
     */
    public $eo;

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
     * @param string $hexColor
     *
     * @return string
     */
    private static function parseHexColor(string $hexColor)
    {
        $hexColor = ltrim($hexColor, '#');
        $len      = strlen($hexColor);
        if ($len < 3) {
            $hexColor = str_pad($hexColor, 3, '0', \STR_PAD_RIGHT);
        } elseif ($len > 3 && $len < 6) {
            $hexColor = str_pad($hexColor, 6, '0', \STR_PAD_RIGHT);
        } elseif ($len > 6) {
            $hexColor = substr($hexColor, 0, 6);
        }

        return '#' . $hexColor;
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

    /**
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    protected function parseKeyValuePair(string $key, $value)
    {
        $key    = strtolower($key);
        $setter = 'set' . $this->getCamelCaseName($key);
        if (method_exists($this, $setter)) {
            $this->{$setter}($value);
        } elseif (property_exists($this, $key)) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @param $value
     */
    public function setBgc($value)
    {
        if (null !== $value) {
            $this->bgc = self::parseHexColor($value);
        }
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
     * @param $value
     *
     * @return $this
     */
    public function setE($value)
    {
        $this->e = strtolower($value);

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
     * @param $value
     *
     * @return $this
     */
    public function setR($value)
    {
        $this->r = (int)$value;

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
                $this->c = self::C_SCALE;
            } elseif ($this->w || $this->h) {
                $this->c = self::C_FIT;
            }
        }

        if ($this->q <= 0 || $this->q > 100 || null === $this->q) {
            $this->q = 75;
        }


        if (null === $this->eo) {
            switch ($this->e) {
                case self::E_BLUR:
                    $this->eo = 1; // for Blur sigma=1
                    break;
                case self::E_COLORIZE:
                    $this->eo = '#FFFFFF'; // color in HEX
                    break;
                case self::E_GAMMA:
                    $this->eo = 1; // gamma correction
                    break;
                default:
                    break;
            }
        } else {
            switch ($this->e) {
                case self::E_COLORIZE:
                    $this->eo = self::parseHexColor($this->eo);
                    break;
                default:
                    break;
            }
        }

        return $this;
    }
}
