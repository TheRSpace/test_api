<?php

namespace app\models;

/**
 * Class Product
 */

class BookProduct extends Product
{
    private $weight;
    public function __construct($id, $sku, $name, $price, $type_name, $attributes) //$productsku, $productname, $productprice, $producttype)
    {
        parent::__construct($id, $sku, $name, $price, $type_name);
        $this->weight = $attributes['weight'];
    }
    public function getWeight()
    {
        return $this->weight;
    }
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
    public function getType()
    {
        return 'weight';
    }
    public function getAttributes()
    {
        return array("weight" => $this->getWeight());
    }
    public function getAssociativeArray()
    {
        return array("id" => $this->getId(), "sku" => $this->getProductSku(), "name" => $this->getProductName(), "price" => $this->getProductPrice(), "type_name" => $this->getProductTypeName(), "weight" => $this->getWeight());
    }
}
