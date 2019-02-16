<?php

class VendorsListMapper extends Mapper {

    public function getVendorsList ($condition="1", $order_by="")
    {
        $sql = "select * from `vendors` where $condition". ($order_by !="" ? " order by $order_by" : "");
        $vendors_rows = $this->db->select($sql);
        return $vendors_rows;
    }

} 