<?php

namespace app\models;

class DigitalProduct extends Product
{
    private $size;
    public function __construct($id, $sku, $name, $price, $type_name, $attributes)
    {
        parent::__construct($id, $sku, $name, $price, $type_name);
        $this->size = $attributes['size'];
    }

    public function getSize()
    {
        return $this->size;
    }
    public function setSize($size)
    {
        $this->size = $size;
    }
    public function getType()
    {
        return 'size';
    }
    public function getAttributes()
    {
        return array("size" => $this->getSize());
    }
    public function getAssociativeArray()
    {
        return array("id" => $this->getId(), "sku" => $this->getProductSku(), "name" => $this->getProductName(), "price" => $this->getProductPrice(), "type_name" => $this->getProductTypeName(), "size" => $this->getSize());
    }
}
