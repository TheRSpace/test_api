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
        if ($products) {
            foreach ($products as $product) {
                $newProducts[] = $product->getAssociativeArray();
            }

            return new Response($newProducts, 200);
        } else {
            return new Response(['info' => 'Products not found'], 200);
        }
    }
    public function getProductTypes()
    {
        $newTypes = [];
        $types = $this->productRepository->getTypes();
        if ($types) {
            foreach ($types as $type) {
                //$newProduct = $this->productFactory->createProduct($row['id'], $row['sku'], $row['name'], $row['price'], $row['type_name'], $row['attributes']);
                $newTypes[] = $type->getAssociativeArray();
            }
            return new Response($newTypes, 200);
        } else {
            return new Response(['error' => 'Product types not found'], 400);
        }
    }
    public function migrate()
    {
        $this->productRepository->migrate();
        return new Response(['message' => 'Hello World'], 200);
    }
    public function showById()
    {
        $id = $this->request->getParams('id');
        $product = $this->productRepository->getById($id);
        if ($product) {
            return new Response($product->getAssociativeArray(), 200);
        } else {
            return new Response(['error' => 'Product not found'], 400);
        }
    }
    public function showBySku()
    {
        $sku = $this->request->getParams('sku');
        $product = $this->productRepository->getBySku($sku);

        if ($product) {
            return new Response($product->getAssociativeArray(), 200);
        } else {
            return new Response(['error' => 'Product ' . $sku . 'not found'], 400);
        }
    }
    public function createProduct()
    {
        $data = $this->request->getBody();
        if ($data) {
            try {
                $product = $this->productRepository->getBySku($data['sku']);
                if ($product) {
                    return new Response(['error' => 'Product with SKU: ' . $data['sku'] . ' already exists'], 400);
                } else {
                    $product = $this->productFactory->createProduct(null, $data['sku'], $data['name'], $data['price'], $data['type_name'], $data['attributes']);
                    $success = $this->productRepository->create($product);
                    if ($success) {
                        return new Response(['message' => 'Product ' . $product->getProductName() . ' created'], 201);
                    } else {
                        return new Response(['error' => 'Failed creating new product!'], 400);
                    }
                }
            } catch (\Exception $e) {
                return new Response(['error' => 'An error occurred:' . $e->getMessage()], 400);
            }
        } else {
            return new Response(['error' => 'No Valid data provided!'], 400);
        }
    }
    public function checkProductSku()
    {
        $sku = $this->request->getParams('sku');
        if ($sku) {
            $product = $this->productRepository->getBySku($sku);
            if ($product) {
                return new Response(["valid" => false], 200);
            } else {
                return new Response(["valid" => true], 200);
            }
        } else {
            return new Response(['error' => 'No Valid data provided!'], 400);
        }
    }
    public function deleteProduct()
    {
        $id = $this->request->getParams('id');
        $product = $this->productRepository->getById($id);
        if ($product) {
            if ($this->productRepository->delete($product)) {
                return new Response(['message' => 'Product ' . $product->getId() . ' deleted'], 200);
            } else {
                return new Response(['error' => 'Failed deleting product'], 400);
            }
        } else {
            return new Response(['error' => 'Product not found'], 400);
        }
    }
    public function deleteProducts()
    {
        $data = $this->request->getBody();
        if (!is_array($data['ids'])) {
            $data['ids'] = [$data['ids']];
        }
        if ($data && isset($data['ids'])) {
            $productIds = $data['ids'];
            $products = $this->productRepository->getByIds($productIds);

            if (count($products) === count($productIds)) {
                $success = $this->productRepository->deleteSelected($products);
                if ($success) {
                    return new Response(['message' => 'Products deleted'], 200);
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

    //?? Just testing an interesting method ignore the comented code
    /* public function getAllFetchClass()
    {
        $newProducts = [];
        $products = $this->productRepository->getAllFetchClass();
        foreach ($products as $product) {
            $newProducts[] = $product->getAssociativeArray2();
        }
        if ($newProducts) {
            return new Response($newProducts, 200);
        } else {
            return new Response(['error' => 'Products not found'], 400);
        }
     }
     */
}
