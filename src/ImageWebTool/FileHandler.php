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
     * @param \mrcnpdlk\ImageWebTool\Bootstrap $oBootstrap
     *
     * @throws \Exception
     */
    public function __construct(Bootstrap $oBootstrap)
    {
        /* clear filename */
        $this->oParams = $oBootstrap->getParams();
        try {
            if (!$oBootstrap->isFileExists()) {
                $oPlaceholder = Placeholder::create($this->oParams->w, $this->oParams->h, $oBootstrap->getExtension());

                if ('placeholder' === strtolower($oBootstrap->getFileName())) {
                    $this->oInputImg = $oPlaceholder->setText()->get();
                } else {
                    $this->oInputImg = $oPlaceholder->setText('Not found')->get();
                }

            } else {
                $this->oInputImg = (new Imagine())->open($oBootstrap->getFileRealpath());
            }
        } catch (\Exception $e) {
            if (Helper::getConfig('debug')) {
                throw $e;
            }
            $oPlaceholder    = Placeholder::create($this->oParams->w, $this->oParams->h);
            $this->oInputImg = $oPlaceholder->get();
        }

        $this->oOutputImg = $this->oInputImg->copy();
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
