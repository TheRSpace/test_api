<?php

namespace app\models;

/**
 * Class ProductClass
 *
 */

class ProductClass
{

    private $id;
    private $product_sku;
    private $product_name;
    private $product_price;
    private $product_type_name;
    public function getAssociativeArray2()
    {
        return array("id" => $this->id, "sku" => $this->product_sku, "name" => $this->product_name, "price" => $this->product_price, "type_name" => $this->product_type_name);
    }
}
