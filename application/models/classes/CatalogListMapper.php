<?php

class CatalogListMapper extends Mapper {

    public function getCatalogList ($parent_catalog_id=0, $condition="1", $order_by="")
    {
        if (is_integer($parent_catalog_id) && ($parent_catalog_id>=0))
        {
            $condition .= " and `parentid` = $parent_catalog_id";
        }
        $sql = "select * from `catalogs` where $condition". ($order_by !="" ? " order by $order_by" : "");
        $catalogs_rows = $this->db->select($sql);
        return $catalogs_rows;
    }

} 