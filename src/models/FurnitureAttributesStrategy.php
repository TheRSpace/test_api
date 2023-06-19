<?php

namespace app\models;

use app\models\ProductAttributesStrategy;

class FurnitureAttributesStrategy implements ProductAttributesStrategy
{
    private $height;
    private $width;
    private $length;

    public function __construct($height, $width, $length)
    {
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getAttributes()
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
        ];
    }
}
