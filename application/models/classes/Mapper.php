<?php

class Mapper {
    protected $db;

    public function __construct(DataBase $db)
    {
        $this->db = $db;
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