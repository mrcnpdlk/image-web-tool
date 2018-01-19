<?php
/**
 * Created by Marcin.
 * Date: 19.01.2018
 * Time: 19:27
 */

namespace mrcnpdlk\ImageWebTool;


use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Font;
use Imagine\Imagick\Imagine;

class Placeholder
{
    /**
     * @var \Imagine\Imagick\Imagine
     */
    private $oImagine;
    /**
     * @var \Imagine\Image\ImageInterface
     */
    private $oInputPlaceholder;
    /**
     * @var \Imagine\Image\ImageInterface
     */
    private $oOutputPlaceholder;


    /**
     * Placeholder constructor.
     *
     * @param int|null $width
     * @param int|null $height
     * @param string   $format
     */
    public function __construct(int $width = null, int $height = null, string $format = 'png')
    {
        $this->oImagine           = new Imagine();
        $this->oInputPlaceholder  = $this->oImagine->create(
            new Box($width ?? 200, $height ?? 200),
            (new RGB())->color('#D3D3D3', 100));
        $this->oOutputPlaceholder = $this->oInputPlaceholder->copy();
        $this->oOutputPlaceholder->getImagick()->setImageFormat($format);

    }

    /**
     * @param int|null $width
     * @param int|null $height
     * @param string   $format
     *
     * @return static
     */
    public static function create(int $width = null, int $height = null, string $format = 'png')
    {
        return new static($width, $height, $format);
    }

    /**
     * @return \Imagine\Image\ImageInterface
     */
    public function get(): \Imagine\Image\ImageInterface
    {
        return $this->oOutputPlaceholder;
    }

    /**
     * Set text on placeholder
     *
     * @param string|null $text If NULL dimension is shown
     *
     * @return $this
     */
    public function setText(string $text = null)
    {
        $text = $text ?? sprintf(
                '%s x %s',
                $this->oOutputPlaceholder->getSize()->getWidth(),
                $this->oOutputPlaceholder->getSize()->getHeight());

        $fontSize = 50;
        do {
            $oFont = new Font(
                $this->oOutputPlaceholder->getImagick(),
                Helper::getConfig()->get('font'),
                $fontSize--,
                (new RGB())->color('#000', 100));

            $oImageSize = $this->oOutputPlaceholder->getSize();
            $oFontSize  = $oFont->box($text);
        } while (!$oImageSize->contains($oFontSize) || $fontSize < 0);

        $oPoint = new Point(
            ($oImageSize->getWidth() - $oFontSize->getWidth()) / 2,
            ($oImageSize->getHeight() - $oFontSize->getHeight()) / 2
        );

        $this->oOutputPlaceholder
            ->draw()
            ->text(
                $text,
                $oFont,
                $oPoint
            )
        ;

        return $this;
    }
}
