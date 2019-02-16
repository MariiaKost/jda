<?php

class CatalogList {

    private $catalogs=array();

    public function __construct (array $catalogs_rows)
    {
        for ($i=0, $c=count($catalogs_rows); $i<$c; $i++)
        {
            $this->catalogs[$i] = new Catalog ($catalogs_rows[$i]['id'], $catalogs_rows[$i]['name']);
        }

    }

   /* public function __get($attr)
    {
        if (isset($this->$attr))
            return $this->$attr;
        else
            die("Атрибут ".$attr." не найден!");
    }*/

    public function &__get($attr)
    {
        if (is_scalar($this->$attr))
        {
            $property = $this->$attr;
        }
        else
        {
            $property = &$this->$attr;
        }
        return $property;
    }



    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }

} 