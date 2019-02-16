<?php
class Cart {
    protected static $_instance;
    private $order = array("product_id"=>array(),"quantity"=>array(),"price"=>array());

    private function __construct ($order=array())
    {
        $this->order = $order;
        // Структура массива $order:
        //array ("product_id"=>array(),"quantity"=>array(),"price"=>array())
        ////////
    }

    private function __clone ()
    {

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

    static public function getCart()
    {
        if (null === self::$_instance)
        {
            $c=count(@$_SESSION['cart']["product_id"]);
            if ($c==0)
            {
                $order = array("product_id"=>array(),"quantity"=>array(),"price"=>array());
            }
            else
            {
                for ($i=0; $i<$c; $i++)
                {
                    $order["product_id"][$i] =  $_SESSION['cart']["product_id"][$i];
                    $order["quantity"][$i] =  $_SESSION['cart']["quantity"][$i];
                    $order["price"][$i] =  $_SESSION['cart']["price"][$i];
                }
            }
            self::$_instance = new self($order);
        }

        return self::$_instance;
    }

    public function saveCart()
    {
        unset($_SESSION ['cart']);
        $_SESSION ['cart'] = $this->order;
    }

    public function addtoCart($productid, $quantity=1, $price)
    {
        if ($productid=="") return false;
        else
        {

            $keys = array_keys ($this->order["product_id"], $productid);
            if (count ($keys)>0)
            {
                $index=$keys[0];
                $this->order["quantity"][$index] +=$quantity;
                $this->order["price"][$index] +=$price;
            }
            else
            {
                $this->order["product_id"][] = $productid;
                $this->order["quantity"][] = $quantity;
                $this->order["price"][] = $price;
            }
            $this->saveCart();
            return true;
        }

    }


    public function deletefromCart($productid="")
    {
        if ($productid=="") return false;
        else
        {
            $keys = array_keys ($this->order["product_id"], $productid);
            if (count($keys)>0)
            {
                array_splice($this->order["product_id"], $keys[0], 1);
                array_splice($this->order["quantity"], $keys[0], 1);
                array_splice($this->order["price"], $keys[0], 1);
                $this->saveCart();
                return true;
            }
            else return false;
        }

    }

    public function inCart($productid="")
    {
        if ($productid=="") return false;
        else
        {
            if (in_array($productid, $this->order["product_id"]))
            {
                return true;
            }
            else return false;

        }

    }

    public function countOfProducts()
    {
        return array_sum($this->order["quantity"]);
    }

}