<?php

namespace app\controllers;

use app\core\Application;
use app\repositories\ProductRepository;
use app\core\Request;
use app\core\Response;
use app\models\ProductFactory;

class ProductController extends ProductRepository
{
    private ProductRepository $productRepository;
    private Request $request;
    private ProductFactory $productFactory;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->request = Application::getApp()->getRequest();
        $this->productFactory = new ProductFactory();
    }

    public function getAll()
    {
        $newProducts = [];
        $products = $this->productRepository->getAll();
        foreach ($products as $row) {
            $newProduct = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
            $newProducts[] = $newProduct->getAssociativeArray();
        }
        if ($newProducts) {
            return new Response($newProducts, 200);
        } else {
            return new Response(['error' => 'Products not found'], 400);
        }
    }
    public function getTypes()
    {
        $newTypes = [];
        $types = $this->productRepository->getTypes();
        foreach ($types as $row) {
            //$newProduct = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
            $newTypes[] = array("id" => $row['id'], "type_name" => $row['type_name']);
        }
        if ($newTypes) {
            return new Response($newTypes, 200);
        } else {
            return new Response(['error' => 'Products not found'], 400);
        }
    }
    public function getAllFetchClass()
    {
        $newProducts = [];
        $products = $this->productRepository->getAllFetchClass();
        foreach ($products as $row) {
            $newProducts[] = $row->getAssociativeArray2();
        }
        if ($newProducts) {
            return new Response($newProducts, 200);
        } else {
            return new Response(['error' => 'Products not found'], 400);
        }
    }
    public function get()
    {
        $this->productRepository->get();
        // $migration = new ProductMigration($db->getConnection(), __DIR__);
        // $migration->applyMigrations();
        return new Response(['message' => 'Hello World'], 200);
    }
    public function showById()
    {
        $id = $this->request->getParams('id');
        $row = $this->productRepository->getById($id);
        if ($row) {
            $product = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
            return new Response($product->getAssociativeArray(), 200);
        } else {
            return new Response(['error' => 'Product not found'], 400);
        }
    }
    public function showBySku()
    {
        $sku = $this->request->getParams('sku');
        $row = $this->productRepository->getBySku($sku);

        if ($row) {
            $product = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
            return new Response($product->getAssociativeArray(), 200);
        } else {
            return new Response(['error' => 'Product ' . $sku . 'not found'], 400);
        }
    }

    public function createProduct()
    {
        //$data = Application::getRequestData();
        $data = $this->request->getBody();
        //$data = $data["productValues"];
        //var_dump($data);

        //return new Response(['message' => 'Product ' . $data . ' created'], 201);
        if ($data) {
            $row = $this->productRepository->getBySku($data['sku']);
            if ($row) {
                return new Response(['error' => 'Product with SKU: ' . $data['sku'] . ' already exists'], 400);
            } else {
                $product = $this->productFactory->createProduct(null, $data['sku'], $data['name'], $data['price'], $data['type_name'], $data['attributes']);
                //insert product
                $id = $this->productRepository->create($product);
                //insert product attributes
                $data = $product->getAttributes();
                $attributes = array_merge($id, $data);
                $this->productRepository->insert($product->getType(), $attributes);

                return new Response(['message' => 'Product ' . $product->getProductName() . ' created'], 201);
            } //return new Response(['message' => 'Product ' . $product->getId() . ' created'], 201);
        } else {
            return new Response(['error' => 'No Valid data provided!'], 400);
        }
    }

    public function checkProductSku()
    {
        //$data = $this->request->getBody();
        $sku = $this->request->getParams('sku');
        //var_dump($data);
        //var_dump($this->request->getParams('sku'));
        if ($sku) {
            $row = $this->productRepository->getBySku($sku);
            if ($row) {
                $response[] = array("valid" => false);
            } else {
                $response[] = array("valid" => true);
            }

            if ($row) {
                return new Response($response, 200);
            } else {
                return new Response(["valid" => true], 200);
            }
        } else {
            return new Response(['error' => 'No Valid data provided!'], 400);
        }
    }

    //TODO update()
    public function updateProduct()
    {
        $id = $this->request->getParams('id');
        $row = $this->productRepository->getById($id);

        if ($row) {
            $product = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
            $data = $this->request->getBody();
            if ($data) {
                $product->setProductName($data['name']);
                $product->setProductPrice($data['price']);
                $product->setProductTypeName($data['type_name']);

                $this->productRepository->update($product);

                return new Response(['message' => 'Product with id:' . $id . ' updated'], 201);
            } else {
                return new Response(['error' => 'No valid data provided!'], 400);
            }
        } else {
            return new Response(['error' => 'Product not found'], 400);
        }
    }
    //TODO destroy()
    public function deleteProduct()
    {
        $id = $this->request->getParams('id');
        $row = $this->productRepository->getById($id);

        if ($row) {
            $this->productRepository->delete($id);
            return new Response(['message' => 'Product ' . $id . ' deleted'], 200);
        } else {
            return new Response(['error' => 'Product not found'], 400);
        }
    }
    public function deleteProducts()
    {
        $data = $this->request->getBody();
        //$id = $this->request->getParams('id');
        if (!is_array($data['ids'])) {
            $data['ids'] = [$data['ids']];
        }
        if ($data && isset($data['ids'])) {
            $productIds = $data['ids'];
            $productIdsString = implode(", ", $productIds);
            $rows = $this->productRepository->getByIds($data['ids']);
            //echo $data;
            if (count($rows) === count($productIds)) {
                $success = $this->productRepository->deleteSelected($productIds);
                if ($success) {
                    return new Response(['message' => 'Products ' . $productIdsString . ' deleted'], 200);
                } else {
                    return new Response(['error' => 'Failed to delete products'], 400);
                }
            } else {
                return new Response(['error' => 'One or more products not found'], 400);
            }
        } else {
            return new Response(['error' => 'No valid data provided'], 400);
        }
    }
    public function handleOptionsDeleteRequest()
    {
        $response = new Response(['message' => "OK"], 200);
        $response->setHeader('Access-Control-Allow-Methods', 'GET, DELETE');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $response->setHeader('Access-Control-Max-Age', '86400');
        $response->send();
        return $response;
    }
}
