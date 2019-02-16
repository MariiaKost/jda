<?php

class ServiceList {
    private $services=array();

    public function __construct (array $services_rows)
    {
        for ($i=0, $c=count($services_rows); $i<$c; $i++)
        {
            $this->services[$i] = new Service ($services_rows[$i]['name'], $services_rows[$i]['description'], $services_rows[$i]['text'], $services_rows[$i]['price'], $services_rows[$i]['vendorid'], $services_rows[$i]['article'], $services_rows[$i]['orig_name']);
            $this->services[$i]->id = $services_rows[$i]['id'];
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
        return count($this->services);
    }

} 