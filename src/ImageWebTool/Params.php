<?php
/**
 * Created by Marcin.
 * Date: 16.01.2018
 * Time: 23:29
 */

namespace mrcnpdlk\ImageWebTool;


class Params
{
    /**
     * @var int|null
     */
    public $w;
    /**
     * @var int|null
     */
    public $h;
    /**
     * @var string|null
     */
    public $c;

    /**
     * Params constructor.
     *
     * @param string $params
     */
    public function __construct(string $params)
    {
        $params = trim($params);
        if ('' !== $params) {
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
}
