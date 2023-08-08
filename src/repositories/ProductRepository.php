<?php

namespace app\repositories;

use app\core\Application;
use app\migrate\ProductMigration;
use app\models\Product;
use app\models\ProductFactory;
use app\models\ProductType;
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
        $typeName = $product->getProductTypeName();
        $productType = $this->getTypeByName($typeName);
        if ($productType) {
            $type_id = $productType->getId();
            $query = "INSERT INTO product(sku, name, price, type_id)VALUES(?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);

            if ($stmt->execute([$sku, $name, $price, $type_id])) {

                if ($stmt->rowCount() > 0) {
                    $product->setId($this->connection->lastInsertId());
                    $id = $product->getId();

                    if ($id) {
                        $data = $product->getAttributes();
                        //array key for id is very important for automated insert() to work
                        $attributes = array_merge(["product_id" => $id], $data);
                        //Insert attributes in database table based on product type
                        if ($this->insert($product->getType(), $attributes)) {
                            return true;
                        } else {
                            $this->delete($product);
                            return false;
                        }
                    }
                }
            }
        }

        return false;
    }
    protected function insert(string $table, array $data): bool
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $query = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES ($placeholders)";

        $stmt = $this->connection->prepare($query);
        return $stmt->execute($values);
    }
    protected function migrate()
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
            $products[] = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
        }

        return $products;
    }
    protected function getTypes(): array
    {
        $types = [];
        $query = "SELECT * FROM product_type";
        $stmt = $this->connection->query($query);
        while ($row = $stmt->fetch()) {
            $types[] = new ProductType($row['id'], $row['type_name']);
        }

        return $types;
    }
    protected function getTypeByName($typeName): ?ProductType
    {
        $query = "SELECT * FROM product_type WHERE type_name=?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$typeName]);
        if ($row = $stmt->fetch()) {
            $type = new ProductType($row['id'], $row['type_name']);
            return $type;
        } else {
            return null;
        }
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
    protected function getById($id): ?Product
    {
        try {
            $query = "SELECT p.*, t.type_name, s.size, d.width, d.height, d.length, w.weight, JSON_OBJECT('height', d.height, 'width', d.width, 'length', d.length, 'weight', w.weight, 'size', s.size) as attributes FROM product p LEFT JOIN product_type t ON t.id = p.type_id LEFT JOIN size s ON s.product_id = p.id LEFT JOIN dimension d ON d.product_id = p.id LEFT JOIN weight w ON w.product_id = p.id WHERE p.id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                return $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
            } else {
                return null;
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
            $rows = $stmt->fetchAll();
            if ($rows) {
                $products = [];
                foreach ($rows as $row) {
                    $product = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
                    $products[] = $product;
                }
                return $products;
            } else {
                return [];
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }
    protected function update($product): bool
    {
        $id = $product->getId();
        $sku = $product->getSku();
        $name = $product->getName();
        $price = $product->getPrice();
        $type = $product->getType();
        $query = "UPDATE product SET name=?, price=?, type_id=? WHERE id=?";
        $stmt  = $this->connection->prepare($query);
        return $stmt->execute([$sku, $name, $price, $type, $id]);
    }
    protected function delete(Product $product): bool
    {
        $id = $product->getId();
        $stmt = $this->connection->prepare("DELETE FROM product WHERE id=?");
        return $stmt->execute([$id]);
    }
    protected function deleteSelected(array $products): bool
    {
        $productIds = array_map(function ($product) {
            return $product->getId();
        }, $products);
        try {
            // Create placeholders for the number of IDs
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            $stmt = $this->connection->prepare("DELETE FROM product WHERE id IN ($placeholders)");
            return $stmt->execute($productIds);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    //?? Just testing an interesting method ignore the comented code
    /*
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
    }*/
}
