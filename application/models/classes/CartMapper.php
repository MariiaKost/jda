<?php

class CartMapper extends Mapper {

    public function saveOrder(Cart $cart, $name, $email, $phone, $address)
    {
        $c=count($cart->order["product_id"]);
        if ($c>0) {
            $sql = "insert into `orders` (`name`, `email`, `phone`, `address`)  values ({?},{?},{?},{?})";
            $order_id = $this->db->query($sql, array($name, $email, $phone, $address));

            if ($order_id) {
                $lines_return = true;
                for ($i = 0; $i < $c; $i++) {
                    $sql_lines = "insert into `order_lines` (`npp`, `orderid`, `productid`, `quantity`, `price`)  values ({?},{?},{?},{?},{?})";
                    $lines_return = $lines_return && $this->db->query($sql_lines, array(($i+1), $order_id, $cart->order["product_id"][$i], $cart->order["quantity"][$i], $cart->order["price"][$i]));
                }
                if ($lines_return) return true;
                else return false;
            }
        }
        else
        {
            return false;
        }
    }

}