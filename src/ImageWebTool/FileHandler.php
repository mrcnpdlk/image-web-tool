<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 22:56
 */

namespace mrcnpdlk\ImageWebTool;


use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Imagick\Imagine;

class FileHandler
{
    /**
     * @var \mrcnpdlk\ImageWebTool\Config
     */
    private $oConfig;
    /**
     * @var \mrcnpdlk\ImageWebTool\Params
     */
    private $oParams;
    /**
     * @var \Imagine\Image\ImageInterface|\Imagine\Imagick\Image
     */
    private $oInputImg;
    /**
     * @var \Imagine\Image\ImageInterface|\Imagine\Imagick\Image
     */
    private $oOutputImg;

    /**
     * FileHandler constructor.
     *
     * @param string $fileName
     *
     * @param string $params
     *
     * @throws \mrcnpdlk\ImageWebTool\Exception
     */
    public function __construct(string $fileName, string $params = null)
    {
        /* clear filename */
        $fileName      = basename($fileName);
        $this->oConfig = new Config([]);
        $filePath      = rtrim($this->oConfig->get('storage', ''), '/') . \DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception(sprintf('File [%s] not exists on storage or is not readable', $fileName));
        }
        $this->oInputImg  = (new Imagine())->open($filePath);
        $this->oOutputImg = clone $this->oInputImg;
        $this->setParams($params);

    }

    /**
     * @param string $params
     *
     * @return $this
     */
    private function setParams(string $params = null)
    {
        $this->oParams = new Params($params);
        $this->oParams->standardize();

        return $this;
    }

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function getBlob(string $format = null): string
    {
        $format = $format ?? $this->oOutputImg->getImagick()->getImageFormat();

        $this->effect();
        $this->resize();
        $this->rotate();

        return $this->oOutputImg
            ->get($format, $this->oParams->getQuality($format));
    }

    /**
     * Effects
     *
     * @return \Imagine\Imagick\Image
     */
    protected function effect()
    {
        if ($this->oParams->e) {
            switch ($this->oParams->e) {
                case Params::E_NEGATIVE:
                    $this->oOutputImg->effects()->negative();
                    break;
                case Params::E_GRAYSCALE:
                    $this->oOutputImg->effects()->grayscale();
                    break;
                case Params::E_BLUR:
                    $this->oOutputImg->effects()->blur($this->oParams->eo);
                    break;
                case Params::E_COLORIZE:
                    $palette = new RGB();
                    $color   = $palette->color($this->oParams->eo);
                    $this->oOutputImg->effects()->colorize($color);
                    break;
                case Params::E_GAMMA:
                    $this->oOutputImg->effects()->gamma($this->oParams->eo);
                    break;
            }
        }

        return $this->oOutputImg;
    }

    /**
     * @return $this
     */
    protected function resize()
    {
        $origBox = $this->oInputImg->getSize();
        $w       = $this->oParams->w ?? $origBox->getWidth();
        $h       = $this->oParams->h ?? $origBox->getHeight();
        $oBox    = new Box($w, $h);
        switch ($this->oParams->c) {
            case Params::C_SCALE:
                $this->oOutputImg->resize($oBox);
                break;
            case Params::C_FIT:
                /**
                 * @todo Problem with resize UP
                 */
                $this->oOutputImg = $this->oInputImg->thumbnail($oBox, ImageInterface::THUMBNAIL_INSET);
                break;
            case Params::C_FILL:
                /**
                 * @todo Problem with resize UP
                 */
                $this->oOutputImg = $this->oInputImg->thumbnail($oBox, ImageInterface::THUMBNAIL_OUTBOUND);
                break;
            default:
                $this->oOutputImg = $this->oInputImg->thumbnail($oBox, ImageInterface::THUMBNAIL_INSET);
                break;
        }

        return $this;
    }

    /**
     * @return \Imagine\Image\ImageInterface|\Imagine\Imagick\Image
     */
    protected function rotate()
    {
        if ($this->oParams->r) {
            if ($this->oParams->bgc) {
                $palette = new RGB();
                $color   = $palette->color($this->oParams->bgc);
            }
            $this->oOutputImg->rotate($this->oParams->r, $color ?? null);
        }

        return $this->oOutputImg;
    }
}
