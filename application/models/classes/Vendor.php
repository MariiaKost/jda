<?php
class Vendor {
    private $id;
    private $name;


public function __construct ($id=0, $name='')
{
    $this->id = $id;
    $this->name = $name;
}

    public function __get($attr)
    {
        if (isset($this->$attr))
            return $this->$attr;
        else
            die("Атрибут ".$attr." не найден!");
    }


    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }


}