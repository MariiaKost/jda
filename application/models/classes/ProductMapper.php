<?php

class ProductMapper extends Mapper {

    public function getProduct ($id)
    {
        $sql = "select * from `products` where `id`=$id";
        $row = $this->db->selectRow($sql);
        if (empty ($row)) { return null; }

        $product = new Product ($row["name"], $row["description"], $row["text"], $row["price"], $row["vendorid"], $row["article"], $row["orig_name"], $row["approved"]);
        $product->id = $row["id"];
        return $product;
    }

    public function saveProduct(Product &$product)
    {
        if ($product->id) {
            $sql = "UPDATE `products` SET `name`={?}, `vendorid`={?}, `description`={?}, `text`={?}, `price`={?}, `article`={?}, `orig_name`={?}, `approved`={?} WHERE `id`=".$product->id;
            if ($this->db->query($sql, array($product->name, $product->vendorid, $product->description, $product->text, $product->price, $product->article, $product->orig_name, $product->approved)))
            {
                return true;
            }
            else
            {
                throw new Exception ($sql. mysql_error());
            }
        }
        else {
            $sql = "INSERT INTO `products` (`name`, `vendorid`, `description`, `text`, `price`, `article`, `orig_name`, `approved`) VALUES ({?},{?},{?},{?},{?},{?},{?},{?})";
            $product->id = $this->db->query($sql, array($product->name, $product->vendorid, $product->description, $product->text, $product->price, $product->article, $product->orig_name, $product->approved));
            if ($product->id)
            {
                return true;
            }
            else
            {
                throw new Exception ($sql.mysql_error());
            }
        }
    }

    public function deleteProduct(Product &$product)
    {
        if ($product->id) {
            $sql = "DELETE from `products`  WHERE `id`=".$product->id;
            if ($this->db->query($sql))
            {
                return true;
            }
            else
            {
                throw new Exception ($sql);
            }
            $this->deleteProductPictures($product);
        }
    }

    public function saveProductPicture(Product &$product, ProductPicture &$picture, $category)
    {
        if ($product->id>0)
        {

        $sql_npp = "select max(`npp`) from `productpicture` where `productid`=".$product->id." and `category`='".$category."'";
        $npp = $this->db->selectCell ($sql_npp);

        if ($npp !==false)
        {
            if (is_null($npp))
            {
                $npp=1;
            }
            else
            {
                $npp++;
            }
            $sql = "INSERT INTO `productpicture` (`productid`, `category`, `npp`, `filename`) VALUES ({?},{?},{?},{?})";
            if ($this->db->query($sql, array($product->id, $category, $npp, basename($picture->path))))
            {
                return true;
            }
            else
            {
                throw new Exception ($sql. mysql_error());
            }
        }
        else
        {
            throw new Exception ($sql_npp. mysql_error());
        }
        }
        else
        {
            throw new Exception ("Не определен id товара!");
        }

     }

    public function getProductPictures(Product &$product)
    {
        $sql = "SELECT * FROM `productpicture` where `productid`=".$product->id;
        $pictures_rows = $this->db->select($sql);

        for ($i=0, $c=count($pictures_rows); $i<$c; $i++)
        {
            $category = $pictures_rows[$i]["category"];
            $npp = $pictures_rows[$i]["npp"];
            $product->pictures["$category"]["$npp"] = new ProductPicture($_SERVER["DOCUMENT_ROOT"]. "/".PICTURE_DIR. "/". $category ."/". $pictures_rows[$i]["filename"]);
        }
    }



    public function deleteProductPictures(Product &$product)
    {
        $sql_pictures_delete = "select * from productpicture where `productid`=".$product->id;
        $pictures_rows = $this->db->select($sql_pictures_delete);

        for ($i = 0; $i < count($pictures_rows); $i++)
        {
            $url = $_SERVER["DOCUMENT_ROOT"]."/".PICTURE_DIR."/".$pictures_rows[$i]['category']."/".$pictures_rows[$i]['filename'];
            unlink($url);
        }

        $sql = "DELETE FROM `productpicture` WHERE `productid`=".$product->id;
        if ($this->db->query($sql))
        {
            return true;
        }
        else
        {
            throw new Exception ($sql);
        }

    }


    //Удалится картинка из всех категорий
    public function deletePicturesByFilename ($filename)
    {
        $sql_pictures_delete = "select * from productpicture where `filename`={?}";
        $pictures_rows = $this->db->select($sql_pictures_delete, array($filename));

        for ($i = 0; $i < count($pictures_rows); $i++)
        {
            $url = $_SERVER["DOCUMENT_ROOT"]."/".PICTURE_DIR."/".$pictures_rows[$i]['category']."/".$pictures_rows[$i]['filename'];
            unlink($url);
        }

        $sql = "DELETE FROM `productpicture` WHERE `filename`={?}";
        if ($this->db->query($sql, array($filename)))
        {
            return true;
        }
        else
        {
            throw new Exception ($sql);
        }

    }



    public function setCatalogs (Product &$product, array $catalog_id = array())
    {
        if (count($catalog_id) > 0)
        {
            for ($catid = 0, $c=count($catalog_id); $catid < $c; $catid++)
            {
                $replace_sql = "replace into `products_catalogs` (`productid`, `catalogid`) values ({?}, {?})";
                $this->db->query($replace_sql, array($product->id, $catalog_id[$catid]));
            }

            $delete_sql = "
                          delete from products_catalogs
                          where
                           `productid`='$product->id'
                            and `catalogid` not in (" .  implode(", ", $catalog_id) . ")
                           ";
            $this->db->query($delete_sql);
        }
        else
        {
            $delete_sql = "
                          delete from products_catalogs
                          where
                           `productid`='$product->id'
                            and `catalogid` not in (" .  implode(", ", $catalog_id) . ")
                           ";
            $this->db->query($delete_sql);
        }


    }


public function getProductCatalogs(Product &$product)
{
    $sql = "SELECT * FROM `products_catalogs` where `productid`=" . $product->id;
    $catalogs_rows = $this->db->select($sql);
    if ($catalogs_rows) {
        for ($i = 0, $c = count($catalogs_rows); $i < $c; $i++) {
            $catalogs[] = $catalogs_rows[$i]["catalogid"];
        }
        return $catalogs;
    } else return array();
}


    public function deleteProductCatalogs(Product &$product)
    {

        $sql = "DELETE FROM `products_catalogs` WHERE `productid`=".$product->id;
        if ($this->db->query($sql))
        {
            return true;
        }
        else
        {
            throw new Exception ($sql);
        }

    }


}

