<?php

class Catalog {

    private $id;
    private $name;

    public function __construct ($id=0, $name='')
    {
        $this->id = $id;
        $this->name = $name;
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



} 