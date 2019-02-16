<?php

class Product {
    private $id = 0;
    private $name;
    private $orig_name;
    private $description;
    private $text;
    private $price;
    private $vendorid;
    private $article;
    private $approved;
    private $seo_url;

    private $pictures;

    public function __construct ($name='', $description='', $text='',$price=0, $vendorid, $article='', $orig_name='', $approved='yes')
    {
        $this->name = $name;
        $this->description = $description;
        $this->text = $text;
        $this->price = $price;
        $this->vendorid = $vendorid;
        $this->article = $article;
        $this->orig_name = $orig_name;
        $this->approved = $approved;
        $this->pictures = array();
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

    public function getDescription()
    {
        $description_len = 300;
        if (!empty($this->description))
        {
            return $this->description;
        }
        elseif (!empty($this->text))
        {
            $str = mb_substr($this->text, 0, $description_len, "UTF-8");
            return $str;
        }
        else return "";
    }

    public function getfirstPicture($category)
    {
        if ($this->pictures["$category"])
        {
            reset($this->pictures["$category"]);
            return current($this->pictures["$category"]);
        }
        else return null;
    }

    /*
    public function setPictures(array $pictures)
    {
        $this->$attr = $value;
    }
    */


}
