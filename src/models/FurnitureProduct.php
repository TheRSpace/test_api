<?php

namespace app\models;

/**
 * Class Product
 */

class FurnitureProduct extends Product
{
    private $height;
    private $width;
    private $length;
    public function __construct($id, $sku, $name, $price, $type_name, $attributes) //$productsku, $productname, $productprice, $producttype)
    {
        parent::__construct($id, $sku, $name, $price, $type_name);
        $this->height = $attributes['height'];
        $this->width = $attributes['width'];
        $this->length = $attributes['length'];
    }

    public function getHeight()
    {
        return $this->height;
    }
    public function setHeight($height)
    {
        $this->height = $height;
    }
    public function getWidth()
    {
        return $this->width;
    }
    public function setWidth($width)
    {
        $this->width = $width;
    }
    public function getLength()
    {
        return $this->length;
    }
    public function setLength($length)
    {
        $this->length = $length;
    }
    public function getType()
    {
        return 'dimension';
    }
    public function getAttributes()
    {
        return array("height" => $this->getHeight(), "width" => $this->getWidth(), "length" => $this->getLength());
    }
    public function getAssociativeArray()
    {
        return array("id" => $this->getId(), "sku" => $this->getProductSku(), "name" => $this->getProductName(), "price" => $this->getProductPrice(), "type_name" => $this->getProductTypeName(), "height" => $this->getHeight(), "width" => $this->getWidth(), "length" => $this->getLength());
    }
}
