<?php

// namespace app\models;

// use \Exception;

// class ProductFactory
// {
//     public function createProduct($id, $sku, $name, $price, $type, $attributes)
//     {
//         if ($attributes !== null && is_string($attributes)) {
//             $attributes = json_decode($attributes, true);
//         }
//         switch ($type) {
//             case 'Furniture':
//                 $product = new FurnitureProduct($id, $sku, $name, $price, $type, $attributes['height'], $attributes['width'], $attributes['length']);
//                 break;
//             case 'DVD':
//                 $product = new DigitalProduct($id, $sku, $name, $price, $type, $attributes['size']);
//                 break;
//             case 'Book':
//                 $product = new BookProduct($id, $sku, $name, $price, $type, $attributes['weight']);
//                 break;
//             default:
//                 throw new Exception("Invalid product type: " . $type);
//         }

//         return $product;
//     }
// }

namespace app\models;

use \Exception;

class ProductFactory
{
    private $productTypeToFactoryClass = [
        'Furniture' => FurnitureProduct::class,
        'DVD' => DigitalProduct::class,
        'Book' => BookProduct::class,
    ];

    public function createProduct($id, $sku, $name, $price, $type, $attributes)
    {
        //Check if product type is valid
        if (!isset($this->productTypeToFactoryClass[$type])) {
            throw new Exception("Invalid product type: " . $type);
        }

        if ($attributes !== null && is_string($attributes)) {
            $attributes = json_decode($attributes, true);
        }

        $productClass = $this->productTypeToFactoryClass[$type];
        return new $productClass($id, $sku, $name, $price, $type, $attributes);
    }
}
