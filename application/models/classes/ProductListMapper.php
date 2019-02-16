<?php

class ProductListMapper extends Mapper {

    public function getProductList ($condition="1", $order_by="")
    {
        $sql = "select * from `products` where $condition". ($order_by !="" ? " order by $order_by" : "");

        $products_rows = $this->db->select($sql);
        if ($products_rows===false)
        {
            $products_rows = array();
        }
        return $products_rows;
    }

} 