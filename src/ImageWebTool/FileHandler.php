<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 22:56
 */

namespace mrcnpdlk\ImageWebTool;


use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Imagick\Imagine;

class FileHandler
{
    /**
     * @var \mrcnpdlk\ImageWebTool\Params
     */
    private $oParams;
    /**
     * @var \Imagine\Imagick\Image
     */
    private $oInputImg;
    /**
     * @var \Imagine\Imagick\Image
     */
    private $oOutputImg;

    /**
     * FileHandler constructor.
     *
     * @param string                        $fileName
     *
     * @param \mrcnpdlk\ImageWebTool\Params $oParams
     *
     * @throws \mrcnpdlk\ImageWebTool\Exception
     */
    public function __construct(string $fileName, Params $oParams)
    {
        /* clear filename */
        $fileName      = basename($fileName);
        $this->oParams = $oParams;
        $filePath      = rtrim(Helper::getConfig()->get('storage', ''), '/') . \DIRECTORY_SEPARATOR . $fileName;
        try {
            if (!is_file($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
                $fileName     = pathinfo(basename($filePath), \PATHINFO_FILENAME);
                $format       = pathinfo(basename($filePath), \PATHINFO_EXTENSION);
                $oPlaceholder = Placeholder::create($this->oParams->w, $this->oParams->h, $format);

                if ('placeholder' === strtolower($fileName)) {
                    $this->oInputImg = $oPlaceholder->setText()->get();
                } else {
                    $this->oInputImg = $oPlaceholder->setText('Not found')->get();
                }

            } else {
                $this->oInputImg = (new Imagine())->open($filePath);
            }
        } catch (\Exception $e) {
            $oPlaceholder    = Placeholder::create($this->oParams->w, $this->oParams->h);
            $this->oInputImg = $oPlaceholder->get();
        }

        $this->oOutputImg = $this->oInputImg->copy();
    }

    /**
     * Effects
     *
     * @return \Imagine\Image\ImageInterface
     */
    protected function effect(): \Imagine\Image\ImageInterface
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
     * @return $this
     */
    protected function resize()
    {

        switch ($this->oParams->c) {
            case Params::C_SCALE:
                $origBox = $this->oInputImg->getSize();
                $w       = $this->oParams->w ?? $origBox->getWidth();
                $h       = $this->oParams->h ?? $origBox->getHeight();
                $oBox    = new Box($w, $h);
                $this->oOutputImg->resize($oBox);
                break;

            case Params::C_FILL:
                $this->oOutputImg = Helper::resizeFill($this->oOutputImg, $this->oParams->w, $this->oParams->h);
                break;
            case Params::C_FIT_MARGIN:
                $this->oOutputImg = Helper::resizeFit($this->oOutputImg, $this->oParams->w, $this->oParams->h);
                $this->oOutputImg = Helper::addMargin($this->oOutputImg, $this->oParams->w, $this->oParams->h);
                break;
            case Params::C_FIT:
            default:
                $this->oOutputImg = Helper::resizeFit($this->oOutputImg, $this->oParams->w, $this->oParams->h);
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
            $palette = new RGB();
            if ($this->oParams->bgc) {
                $color = $palette->color($this->oParams->bgc);
            } else {
                $color = $palette->color('#FFF', 0);
            }
            $this->oOutputImg->rotate($this->oParams->r, $color);
        }

        return $this->oOutputImg;
    }
}
