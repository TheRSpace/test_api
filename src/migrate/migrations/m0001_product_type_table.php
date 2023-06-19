<?php

namespace app\migrate\migrations;

class m0001_product_type_table
{
    public function up($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS product_type (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type_name ENUM('Furniture', 'DVD', 'Book'),
            CONSTRAINT uk_type_name UNIQUE (type_name)
        );";
        $db->exec($sql);
        $dummyData = "INSERT INTO product_type (type_name) VALUES
        ('Furniture'),
        ('DVD'),
        ('Book')";
        $db->exec($dummyData);
    }
    public function down($db)
    {
        $sql = "DROP TABLE product_type;";
        $db->getConnection()->exec($sql);
    }
}
