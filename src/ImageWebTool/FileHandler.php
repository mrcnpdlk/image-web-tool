<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 22:56
 */

namespace mrcnpdlk\ImageWebTool;


use Imagine\Image\Box;
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
    private $oImg;

    /**
     * FileHandler constructor.
     *
     * @param string $fileName
     *
     * @param string $params
     *
     * @throws \mrcnpdlk\ImageWebTool\Exception
     */
    public function __construct(string $fileName, string $params)
    {
        /* clear filename */
        $fileName      = basename($fileName);
        $this->oConfig = new Config([]);
        $filePath      = rtrim($this->oConfig->get('storage', ''), '/') . \DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception(sprintf('File [%s] not exists on storage or is not readable', $fileName));
        }
        $this->oImg = (new Imagine())->open($filePath);
        $this->setParams($params);

    }

    protected function resize()
    {
        $origBox = $this->oImg->getSize();
        $w       = $this->oParams->w ?? $origBox->getWidth();
        $h       = $this->oParams->h ?? $origBox->getHeight();
        $this->oImg->resize(new Box($w, $h));
    }

    /**
     * @param string $params
     *
     * @return $this
     */
    private function setParams(string $params)
    {
        $this->oParams = new Params($params);

        return $this;
    }

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function getBlob(string $format = null): string
    {
        $format = $format ?? $this->oImg->getImagick()->getImageFormat();

        $this->resize();

        return $this->oImg
            ->get($format);
    }
}
