<?php

class VendorMapper extends Mapper {

    public function getVendor ($id=0, $name='')
    {
        if ($id>0)
        {
            $sql = "select `name` from `vendors` where `id`=$id";
            $name = $this->db->selectCell($sql);
            if ($name===false)
            {
                return false;
            }
        }
        elseif ($name<>"")
        {
            $sql = "select `id` from `vendors` where `id`=$id";
            $id = $this->db->selectCell($sql);
            if ($id===false)
            {
                return false;
            }
        }
        else
        {
            return false;
        }

        $vendor = new Vendor ($id, $name);
        return $vendor;
    }

} 