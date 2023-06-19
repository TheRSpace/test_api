<?php

namespace app\migrate\migrations;

class m0003_size_table
{
    public function up($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS size (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            size VARCHAR(50),
            CONSTRAINT fk_product_size
                FOREIGN KEY (product_id)
                REFERENCES product(id)
                ON DELETE CASCADE
        );";
        $db->exec($sql);
    }
    public function down($db)
    {
        $sql = "DROP TABLE size;";
        $db->getConnection()->exec($sql);
    }
}
