<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 22:56
 */

namespace mrcnpdlk\ImageWebTool;


use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
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

        $this->resize();

        return $this->oOutputImg
            ->get($format,$this->oParams->getQuality($format));
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
            case Params::W_SCALE:
                $this->oOutputImg->resize($oBox);
                break;
            case Params::W_FIT:
                /**
                 * @todo Problem with resize UP
                 */
                $this->oOutputImg = $this->oInputImg->thumbnail($oBox, ImageInterface::THUMBNAIL_INSET);
                break;
            case Params::W_FILL:
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
}
