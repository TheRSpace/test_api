<?php

namespace app\migrate\migrations;

use app\core\Application;

class m0002_products_table
{
    public function up($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS product(
            id INT AUTO_INCREMENT PRIMARY KEY,
            sku VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            type_id  INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT uk_product_sku UNIQUE (sku),
            CONSTRAINT fk_product_product_type
                FOREIGN KEY (type_id)
                REFERENCES product_type(id)
        );";
        $db->exec($sql);

        $dummyData = "INSERT INTO product (sku, name, price, type_id) VALUES
        ('SKU-001', 'Product 1', 9.99, 1),
        ('SKU-002', 'Product 2', 19.99, 2),
        ('SKU003', 'Product 3', 29.99, 3),
        ('SKU004', 'Product 4', 39.99, 1),
        ('SKU005', 'Product 5', 49.99, 2),
        ('SKU006', 'Product 6', 59.99, 3),
        ('SKU007', 'Product 7', 69.99, 1),
        ('SKU008', 'Product 8', 79.99, 2),
        ('SKU009', 'Product 9', 89.99, 3),
        ('SKU010', 'Product 10', 99.99, 1);";
        $db->exec($dummyData);
    }
    public function down($db)
    {
        $sql = "DROP TABLE product;";
        $db->getConnection()->exec($sql);
    }
}
