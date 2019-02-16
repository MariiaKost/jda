<?php

class ServiceListMapper extends Mapper {

    public function getServiceList ($condition="1", $order_by="")
    {
        $sql = "select * from `services` where $condition". ($order_by !="" ? " order by $order_by" : "");

        $services_rows = $this->db->select($sql);
        if ($services_rows===false)
        {
            $services_rows = array();
        }
        return $services_rows;
    }

} 