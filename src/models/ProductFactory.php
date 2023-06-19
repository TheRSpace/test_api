<?php

namespace app\models;

use \Exception;

class ProductFactory
{
    public function createProduct($id, $sku, $name, $price, $type, $attributes)
    {
        if ($attributes !== null && is_string($attributes)) {
            $attributes = json_decode($attributes, true);
        }
        switch ($type) {
            case 'Furniture':
                $product = new FurnitureProduct($id, $sku, $name, $price, $type, $attributes['height'], $attributes['width'], $attributes['length']);
                break;
            case 'DVD':
                $product = new DigitalProduct($id, $sku, $name, $price, $type, $attributes['size']);
                break;
            case 'Book':
                $product = new BookProduct($id, $sku, $name, $price, $type, $attributes['weight']);
                break;
            default:
                throw new Exception("Invalid product type: " . $type);
        }

        return $product;
    }
}
