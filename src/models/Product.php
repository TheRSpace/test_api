<?php

namespace app\models;

/**
 * Class Product
 */

abstract class Product
{
    private $id;
    private $productSku;
    private $productName;
    private $productPrice;
    private $productTypeName;

    public function __construct($id, $sku, $name, $price, $typeName) //$productsku, $productname, $productprice, $producttype)
    {
        $this->setId($id);
        $this->setProductSku($sku);
        $this->setProductName($name);
        $this->setProductPrice($price);
        $this->setProductTypeName($typeName);
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getProductSku()
    {
        return $this->productSku;
    }

    public function setProductSku($sku)
    {
        // Validate SKU format
        if (!preg_match("/^[A-Za-z0-9\-]+$/", $sku)) {
            throw new \Exception("Invalid SKU format");
        }
        $this->productSku = $sku;
    }

    public function getProductName()
    {
        return $this->productName;
    }

    public function setProductName($name)
    {
        // Validate name format
        if (!preg_match("/^[A-Za-z0-9\-\s]+$/", $name)) {
            throw new \Exception("Invalid name format");
        }
        $this->productName = $name;
    }

    public function getProductPrice()
    {
        return $this->productPrice;
    }

    public function setProductPrice($price)
    {
        // Validate price format
        if (!is_numeric($price)) {
            throw new \Exception("Invalid price format");
        }
        $this->productPrice = $price;
    }

    public function getProductTypeName()
    {
        return $this->productTypeName;
    }

    public function setProductTypeName($type)
    {
        $this->productTypeName = $type;
    }
    abstract public function getType();
    abstract public function getAttributes();
    public function getAssociativeArray()
    {
        return array("id" => $this->getId(), "sku" => $this->getProductSku(), "name" => $this->getProductName(), "price" => $this->getProductPrice(), "type_name" => $this->getProductTypeName());
    }
}
