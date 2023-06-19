<?php

// namespace app\controllers;



// use app\repository\ProductRepository;
// use app\services\ProductService;

// use app\core\ResponseFactory;
// use \Psr\Http\Message\ResponseInterface as Response;
// use \Psr\Http\Message\ServerRequestInterface as Request;

// /**
//  * Class ProductsController
//  * handles CRUD requests
//  */
// class ProductController extends ProductRepository
// {
//     private $conn;
//     private ProductService $productService;
//     private ResponseFactory $responseFactory;
//     public function __construct(ProductService $productService, ResponseFactory $responseFactory)
//     {
//         $this->productService = $productService;
//         $this->responseFactory = $responseFactory;
//         //create connection.
//         //$this->conn = (new \DatabaseHost())->getConnection();
//     }
//     public function createProduct(Request $request, Response $response, array $args): Response
//     {
//         $data = $request->getParsedBody();
//         $product = $this->productService->createProduct($data['productsku'], $data['productname'], $data['productprice'], $data['producttype']);

//         $payload = json_encode(['product_id' => $product->getId()]);
//         $response->getBody()->write($payload);

//         return $this->responseFactory->createResponse($response, 201);
//     }
//     public function getProduct(Request $request, Response $response, array $args): Response
//     {
//         $id = (int) $args['id'];
//         $user = $this->productService->getProductById($id);

//         $payload = json_encode($user->toArray());
//         $response->getBody()->write($payload);

//         return $this->responseFactory->createResponse($response);
//     }
//     public function getAll(Request $request, Response $response, array $args): Response
//     {

//         $user = $this->productService->getAll();

//         $payload = json_encode($user->toArray());
//         $response->getBody()->write($payload);

//         return $this->responseFactory->createResponse($response);
//     }
// }
