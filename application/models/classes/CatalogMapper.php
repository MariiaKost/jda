<?php

class CatalogMapper extends Mapper {

    public function getCatalog ($id=0, $name='')
    {
        if ($id>0)
        {
            $sql = "select `name` from `catalogs` where `id`=$id";
            $name = $this->db->selectCell($sql);
            if ($name===false)
            {
                return false;
            }
        }
        elseif ($name<>"")
        {
            $sql = "select `id` from `catalogs` where `name`={?}";
            $id = $this->db->selectCell($sql, array($name));
            if ($id===false)
            {
                return false;
            }
        }
        else
        {
            return false;
        }

        $catalog = new Catalog ($id, $name);
        return $catalog;
    }


    public function saveCatalog(Catalog &$catalog, $parent_id=0)
    {
        if ($catalog->id) {
            $sql = "UPDATE `catalogs` SET `name`={?}, `parentid`={?} WHERE `id`=".$catalog->id;
            if ($this->db->query($sql, array($catalog->name, $parent_id)))
            {
                return true;
            }
            else
            {
                throw new Exception ($sql. mysql_error());
            }
        }
        else {
            $sql = "INSERT INTO `catalogs` (`name`, `parentid`) VALUES ({?},{?})";
            $catalog->id = $this->db->query($sql, array($catalog->name, $parent_id));
            if ($catalog->id)
            {
                return true;
            }
            else
            {
                throw new Exception ($sql.mysql_error());
            }
        }
    }

    public function deleteCatalog(Catalog &$catalog)
    {
        if ($catalog->id) {
            $sql = "DELETE from `catalogs`  WHERE `id`=".$catalog->id;
            if ($this->db->query($sql))
            {
                return true;
            }
            else
            {
                throw new Exception ("Не удалось удалить каталог!");
            }
        }
    }

    public function hasChildren(Catalog &$catalog)
    {
        if ($catalog->id==0)
        {
            if ($catalog->name <>"")
            {
                $sql = "select `id` from `catalogs` where `name`={?}";
                $id = $this->db->selectCell($sql, array($catalog->name));
                if ($id===false)
                {
                    throw new Exception ("Каталог с таким именем отсутствует!");
                }
                else
                {
                    $catalog->id = $id;
                }
            }
            else
            {
                throw new Exception ("Необходимо указать id либо имя каталога!");
            }
        }
        else
        {
            $sql = "select * from `catalogs`  WHERE `parentid`=".$catalog->id;
            $res = $this->db->select($sql);
            if ($res !== false)
            {
                if (count($res)>0)
                {
                    return true;
                }
                else
                {
                    return false;
                }

            }
            else
            {
                throw new Exception ($sql);
            }
        }
    }

    public function hasProducts(Catalog &$catalog)
    {
        if ($catalog->id==0)
        {
            if ($catalog->name <>"")
            {
                $sql = "select `id` from `catalogs` where `name`={?}";
                $id = $this->db->selectCell($sql, array($catalog->name));
                if ($id===false)
                {
                    throw new Exception ("Каталог с таким именем отсутствует!");
                }
                else
                {
                    $catalog->id = $id;
                }
            }
            else
            {
                throw new Exception ("Необходимо указать id либо имя каталога!");
            }
        }
        else
        {
            $sql = "select * from `products_catalogs`  WHERE `catalogid`=".$catalog->id;
            $res = $this->db->select($sql);
            if ($res !== false)
            {
                if (count($res)>0)
                {
                    return true;
                }
                else
                {
                    return false;
                }

            }
            else
            {
                throw new Exception ($sql);
            }
        }
    }


    public function getName ($catalog_id)
    {
        if ($catalog_id>0)
        {
            $sql = "select `name` from `catalogs` where `id`=$catalog_id";
            $name = $this->db->selectCell($sql);
            if ($name===false)
            {
                return false;
            }
        }
        else
        {
            return false;
        }

        return $name;
    }

    public function getParentID($catalog_id)
    {
         $sql = "select `parentid` from `catalogs`  WHERE `id`=".$catalog_id;
         $parent_id = $this->db->selectCell($sql);
         return $parent_id;
    }

    public function getPath($catalog_id)
    {
        $path = array();
        $parent_id = $this->getParentID($catalog_id);

        while ($parent_id !=0)
        {
            $cat = $this->getCatalog($parent_id);
            $path[] = $cat;
            $parent_id = $this->getParentID($cat->id);
        }

        return $path;

    }


    public function getChildrenCatalogs($catalog_id)
    {
        $id_array = array();
        $sql = "select * from `catalogs`  WHERE `parentid`=$catalog_id";
        $res = $this->db->select($sql);

        if ($res !== false)
        {
            foreach ($res as $v)
            {
                $id_array[] = $v["id"];
            }
        }
        return $id_array;
    }

    public function getProductsID($catalog_id)
    {
        $id_array = array();
        $sql = "select * from `products_catalogs`  WHERE `catalogid`=$catalog_id";
        $res = $this->db->select($sql);

        if ($res !== false)
        {
            foreach ($res as $v)
            {
                $id_array[] = $v["productid"];
            }
        }
        else
        {
            throw new Exception ("Товаров нет");
        }
        return $id_array;
    }

    public function getAllChildrenCatalogs($catalog_id, $catalogs_id_array = array())
    {
        $children_catalogs = $this->getChildrenCatalogs($catalog_id);

        if(!empty ($children_catalogs))
        {
            $catalogs_id_array = array_merge($catalogs_id_array, $children_catalogs);

            foreach ($children_catalogs as $v)
            {
                $catalogs_id_array = $this->getAllChildrenCatalogs ($v, $catalogs_id_array);
            }

        }

        return $catalogs_id_array;
    }


    public function getProductsIDWithChildren($catalog_id)
    {
        $products_id_array = array();

        $catalogs_id_array = array_merge(array($catalog_id), $this->getAllChildrenCatalogs($catalog_id));

        foreach ($catalogs_id_array as $v)
        {
            $products_id_array = array_merge($products_id_array, $this->getProductsID($v));
        }

        return $products_id_array;


    }



}

