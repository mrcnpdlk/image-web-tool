<?php
/**
 * Created by Marcin.
 * Date: 17.01.2018
 * Time: 20:07
 */

namespace mrcnpdlk\ImageWebTool;


use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;

class Helper
{
    /**
     * @param \Imagine\Image\ImageInterface $oOrigImage
     * @param int                           $iThumbWidth
     * @param int                           $iThumbHeight
     *
     * @return \Imagine\Image\ImageInterface
     */
    public static function addMargin(Image $oOrigImage, int $iThumbWidth, int $iThumbHeight)
    {
        /**
         * @var \Imagine\Image\ImageInterface $thumb
         * @var \Imagine\Image\ImageInterface $preserve
         * @var \Imagine\Image\ImageInterface $oImage
         */
        $oImagine = new Imagine();
        //hack na CMYKa - wstawiał obrazek w negatywie
        if (strtolower($oOrigImage->palette()->name()) !== 'rgb') {
            $oOrigImage->usePalette(new RGB());
        }
        $widthR   = $oOrigImage->getSize()->getWidth();
        $heightR  = $oOrigImage->getSize()->getHeight();
        $preserve = $oImagine->create(new Box($iThumbWidth, $iThumbHeight), (new RGB())->color('#FFF', 0));
        $startX   = 0;
        $startY   = 0;
        if ($widthR < $iThumbWidth) {
            $startX = ($iThumbWidth - $widthR) / 2;
        }
        if ($heightR < $iThumbHeight) {
            $startY = ($iThumbHeight - $heightR) / 2;
        }
        $preserve->paste($oOrigImage, new Point($startX, $startY));

        return $preserve;
    }

    /**
     * @param \Imagine\Image\ImageInterface $oOrigImage
     * @param int|null                      $iThumbWidth
     * @param int|null                      $iThumbHeight
     *
     * @return \Imagine\Image\ImageInterface
     */
    public static function resizeFill(ImageInterface $oOrigImage, int $iThumbWidth = null, int $iThumbHeight = null)
    {
        /**
         * @var \Imagine\Image\ImageInterface $thumb
         * @var \Imagine\Image\ImageInterface $preserve
         * @var \Imagine\Image\ImageInterface $oImage
         */

        $oImage = $oOrigImage->copy();
        $oSize  = $oImage->getSize();

        $imgW = $oSize->getWidth();
        $imgH = $oSize->getHeight();

        $ratio = max(
            $iThumbHeight ? $iThumbHeight / $imgH : $iThumbWidth / $imgW,
            $iThumbWidth ? $iThumbWidth / $imgW : $iThumbHeight / $imgH
        );

        $box          = $oSize->scale($ratio);
        $iThumbWidth  = $iThumbWidth ?? $imgW;
        $iThumbHeight = $iThumbHeight ?? $imgH;

        if ($ratio <= 1) {
            return $oImage->thumbnail(
                new Box($iThumbWidth, $iThumbHeight),
                ImageInterface::THUMBNAIL_OUTBOUND);
        }

        $oImage->resize($box);

        // cropping image
        $oImageSize = $oImage->getSize();
        $oCropBox   = new Box($iThumbWidth, $iThumbHeight);
        $bFactor    = ($iThumbWidth / $iThumbHeight) >= ($oImageSize->getWidth() / $oImageSize->getHeight());

        if (!$bFactor) { // wycinamy pasy po bokach
            return $oImage->crop(new Point(($oImageSize->getWidth() - $oCropBox->getWidth()) / 2, 0), $oCropBox);
        }

        // wycinamy pasy na górze i dole
        return $oImage->crop(new Point(0, ($oImageSize->getHeight() - $oCropBox->getHeight()) / 2), $oCropBox);

    }

    /**
     * @param \Imagine\Image\ImageInterface $oOrigImage
     * @param int|null                      $iThumbWidth
     * @param int                           $iThumbHeight
     *
     * @return \Imagine\Image\ImageInterface
     */
    public static function resizeFit(ImageInterface $oOrigImage, int $iThumbWidth = null, int $iThumbHeight = null)
    {
        if (null === $iThumbHeight && null === $iThumbWidth) {
            return $oOrigImage->copy();
        }
        /**
         * @var \Imagine\Imagick\Image $thumb
         * @var \Imagine\Imagick\Image $preserve
         */

        $oImage = $oOrigImage->copy();
        $oSize  = $oImage->getSize();

        $imgW = $oSize->getWidth();
        $imgH = $oSize->getHeight();


        $ratio = min(
            null === $iThumbWidth ? $iThumbHeight / $imgH : $iThumbWidth / $imgW,
            null === $iThumbHeight ? $iThumbWidth / $imgW : $iThumbHeight / $imgH
        );

        $box = $oSize->scale($ratio);

        if ($ratio > 1) {
            return $oImage->resize($box);
        }

        return $oImage->thumbnail($box, ImageInterface::THUMBNAIL_INSET);
    }
}
