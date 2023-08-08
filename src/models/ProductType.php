<?php

namespace app\models;

/**
 * Class Product
 */

class ProductType
{
    private $id;
    private $typeName;

    public function __construct($id, $typeName)
    {
        $this->setId($id);
        $this->setTypeName($typeName);
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getTypeName()
    {
        return $this->typeName;
    }
    public function getAssociativeArray()
    {
        return array("id" => $this->getId(), "type_name" => $this->getTypeName());
    }
}
