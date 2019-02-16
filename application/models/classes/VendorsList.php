<?php
class VendorsList {

    private $vendors=array();

    public function __construct (array $vendors_rows)
    {
        for ($i=0, $c=count($vendors_rows); $i<$c; $i++)
        {
            $this->vendors[$i] = new Vendor ($vendors_rows[$i]['id'], $vendors_rows[$i]['name']);
        }

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