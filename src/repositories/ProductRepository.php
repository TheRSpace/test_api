<?php

namespace app\repositories;

use app\core\Application;
use app\migrate\ProductMigration;
use app\models\Product;
use app\models\ProductFactory;
use \Exception;
use \PDO;


/**
 * Class ProductRepository
 */

class ProductRepository
{

    private PDO $connection;
    private ProductFactory $productFactory;

    public function __construct()
    {
        $this->connection = Application::getApp()->getDbHost()->getConnection();
        $this->productFactory = new ProductFactory();
    }

    protected function create(Product $product)
    {
        $sku = $product->getProductSku();
        $name = $product->getProductName();
        $price = $product->getProductPrice();
        $type_name = $product->getProductTypeName();
        //var_dump($type_name);
        $type_id = $this->getTypeId($type_name)["id"];
        //var_dump($type_id["id"]);
        $query = "INSERT INTO product(sku, name, price, type_id)VALUES(?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$sku, $name, $price, $type_id]);
        $product->setId($this->connection->lastInsertId());


        return ["product_id" => $this->connection->lastInsertId()];
    }
    protected function insert($table, $data)
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $query = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES ($placeholders)";

        $stmt = $this->connection->prepare($query);
        $stmt->execute($values);
    }
    protected function get()
    {
        $migration = new ProductMigration($this->connection, Application::getApp()->getRootDir());
        $migration->applyMigrations();
    }
    protected function getAll()
    {
        $products = [];

        $query = "SELECT p.*, t.type_name, s.size, d.width, d.height, d.length, w.weight, JSON_OBJECT('height', d.height, 'width', d.width, 'length', d.length, 'weight', w.weight, 'size', s.size) as attributes FROM product p LEFT JOIN product_type t ON t.id = p.type_id LEFT JOIN size s ON s.product_id = p.id LEFT JOIN dimension d ON d.product_id = p.id LEFT JOIN weight w ON w.product_id = p.id ORDER BY p.id";
        $stmt = $this->connection->query($query);
        while ($row = $stmt->fetch()) {
            //var_dump($row);
            $products[] = $row;
        }
        //$products = $stmt->fetchAll();

        return $products;
    }
    protected function getTypes()
    {
        $types = [];

        $query = "SELECT * FROM product_type";
        $stmt = $this->connection->query($query);
        while ($row = $stmt->fetch()) {
            //var_dump($row);
            $types[] = $row;
        }
        //$products = $stmt->fetchAll();

        return $types;
    }
    protected function getTypeId($typeName)
    {

        $query = "SELECT * FROM product_type WHERE type_name=?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$typeName]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return [];
        }
    }
    protected function getAllFetchClass()
    {
        $products = [];

        $query = "SELECT * FROM product";
        $stmt = $this->connection->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "app\models\ProductClass");
        while ($row = $stmt->fetch()) {
            $products[] = $row;
        }
        //$products = $stmt->fetchAll();
        //$stmt = $this->connection->prepare($query);

        //some method of fetch object
        // $objects = [];
        // while ($obj = $stmt->fetchObject("app\models\Product", [2, "rw3", "name", "231", 1])) {
        //     $objects[$obj->getId()] = $obj;
        // }
        //var_dump($objects);
        //print_r($objects);

        //other methode fetch objects
        //$result = $stmt->fetchObject("app\models\Product", [$this->connection]);
        //require_once(dirname(__DIR__) . "/models/Product.php");
        //$result = $stmt->fetchAll(\PDO::FETCH_CLASS, "app\models\Product");

        return $products;
    }

    protected function getBySku($sku): ?Product
    {
        $query = "SELECT p.*, t.type_name, s.size, d.width, d.height, d.length, w.weight, JSON_OBJECT('height', d.height, 'width', d.width, 'length', d.length, 'weight', w.weight, 'size', s.size) as attributes FROM product p LEFT JOIN product_type t ON t.id = p.type_id LEFT JOIN size s ON s.product_id = p.id LEFT JOIN dimension d ON d.product_id = p.id LEFT JOIN weight w ON w.product_id = p.id WHERE p.sku = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$sku]);
        $row = $stmt->fetch();
        if ($row) {

            return $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
        } else {
            return null;
        }
    }

    protected function getById($id): array
    {
        try {
            $query = "SELECT p.*, t.type_name, s.size, d.width, d.height, d.length, w.weight, JSON_OBJECT('height', d.height, 'width', d.width, 'length', d.length, 'weight', w.weight, 'size', s.size) as attributes FROM product p LEFT JOIN product_type t ON t.id = p.type_id LEFT JOIN size s ON s.product_id = p.id LEFT JOIN dimension d ON d.product_id = p.id LEFT JOIN weight w ON w.product_id = p.id WHERE p.id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$id]);

            if ($row = $stmt->fetch()) {
                return $row;
            } else {
                return [];
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }
    protected function getByIds(array $ids): array
    {
        try {
            // Create placeholders for the number of IDs
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $query = "SELECT p.*, t.type_name, s.size, d.width, d.height, d.length, w.weight, JSON_OBJECT('height', d.height, 'width', d.width, 'length', d.length, 'weight', w.weight, 'size', s.size) as attributes FROM product p LEFT JOIN product_type t ON t.id = p.type_id LEFT JOIN size s ON s.product_id = p.id LEFT JOIN dimension d ON d.product_id = p.id LEFT JOIN weight w ON w.product_id = p.id WHERE p.id IN ($placeholders)";

            $stmt = $this->connection->prepare($query);
            $stmt->execute($ids);

            return $stmt->fetchAll();
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    protected function update($product)
    {
        $id = $product->getId();
        $sku = $product->getSku();
        $name = $product->getName();
        $price = $product->getPrice();
        $type = $product->getType();
        $query = "UPDATE product SET name=?, price=?, type_id=? WHERE id=?";
        $stmt  = $this->connection->prepare($query);
        $stmt->execute([$sku, $name, $price, $type, $id]);
    }

    protected function delete(Product $product)
    {
        $id = $product->getId();
        $stmt = $this->connection->prepare("DELETE FROM product WHERE id=?");
        $stmt->execute([$id]);
    }
    protected function deleteSelected(array $products)
    {
        $productIds = array_map(function ($product) {
            return $product->getId();
        }, $products);
        try {
            // Create placeholders for the number of IDs
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            $stmt = $this->connection->prepare("DELETE FROM product WHERE id IN ($placeholders)");
            $stmt->execute($productIds);
            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }
}
