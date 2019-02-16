<?php

class ProductList {
    private $products=array();

    public function __construct (array $products_rows)
    {
        for ($i=0, $c=count($products_rows); $i<$c; $i++)
        {
            $this->products[$i] = new Product ($products_rows[$i]['name'], $products_rows[$i]['description'], $products_rows[$i]['text'], $products_rows[$i]['price'], $products_rows[$i]['vendorid'], $products_rows[$i]['article'], $products_rows[$i]['orig_name']);
            $this->products[$i]->id = $products_rows[$i]['id'];
        }

    }

/*
    public function __get($attr)
    {
        if (isset($this->$attr))
            return $this->$attr;
        else
            die("Атрибут ".$attr." не найден!");
    }
*/

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

    public function getElementsNumber()
    {
        return count($this->products);
    }

} 