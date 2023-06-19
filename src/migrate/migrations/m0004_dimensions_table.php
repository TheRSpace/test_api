<?php

namespace app\migrate\migrations;

class m0004_dimensions_table
{
    public function up($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS dimension (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            height DECIMAL(5, 1),
            width DECIMAL(5, 1),
            length DECIMAL(5, 1),
            CONSTRAINT fk_product_dimension
                FOREIGN KEY (product_id)
                REFERENCES product(id)
                ON DELETE CASCADE
        );";
        $db->exec($sql);
    }
    public function down($db)
    {
        $sql = "DROP TABLE dimension;";
        $db->getConnection()->exec($sql);
    }
}
