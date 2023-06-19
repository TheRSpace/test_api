<?php

namespace app\migrate\migrations;

class m0005_weight_table
{
    public function up($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS weight (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            weight DECIMAL(5, 2),
            CONSTRAINT fk_product_weight
                FOREIGN KEY (product_id)
                REFERENCES product(id)
                ON DELETE CASCADE
        );";
        $db->exec($sql);
    }
    public function down($db)
    {
        $sql = "DROP TABLE weight;";
        $db->getConnection()->exec($sql);
    }
}
