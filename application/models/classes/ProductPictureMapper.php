<?php

class ProductPictureMapper extends Mapper {

    public function getPicture($filename, $category)
    {
        $sql = "select * from `productpicture` where `category`='{?}' and `filename`='{?}'";
        $row = $this->db->selectRow($sql, array($category, $filename));
        if (empty ($row)) { return null; }

        $picture = new ProductPicture ($_SERVER["DOCUMENT_ROOT"]."/".PICTURE_DIR."/".$category."/".$filename);
        return $picture;

    }

    public function deletePicture(ProductPicture &$picture)
    {
        $filename = $picture->getFileName();
        $category = $picture->getCategory();
            $sql = "DELETE from `productpicture`  WHERE `category`='{?}' and `filename`='{?}'";
            if ($this->db->query($sql, array($category, $filename)))
            {
                if (unlink($picture->path))
                {
                    return true;
                }
                else
                {
                    throw new Exception ("unlink error! (".$picture->path.")");
                }
            }
            else
            {
                throw new Exception ($sql);
            }
    }

}


