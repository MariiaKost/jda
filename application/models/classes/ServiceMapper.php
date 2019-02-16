<?php

class ServiceMapper extends Mapper {

    public function getService ($id)
    {
        $sql = "select * from `services` where `id`=$id";
        $row = $this->db->selectRow($sql);
        if (empty ($row)) { return null; }

        $service = new Service ($row["name"], $row["description"], $row["text"], $row["price"], $row["vendorid"], $row["article"], $row["orig_name"]);
        $service->id = $row["id"];
        return $service;
    }

    public function saveService(Service &$service)
    {
        if ($service->id) {
            $sql = "UPDATE `services` SET `name`={?}, `vendorid`={?}, `description`={?}, `text`={?}, `price`={?} WHERE `id`=".$service->id;
            if ($this->db->query($sql, array($service->name, $service->vendorid, $service->description, $service->text, $service->price)))
            {
                return true;
            }
            else
            {
                throw new Exception ($sql. mysql_error());
            }
        }
        else {
            $sql = "INSERT INTO `services` (`name`, `vendorid`, `description`, `text`, `price`, `article`, `orig_name`) VALUES ({?},{?},{?},{?},{?},{?},{?})";
            $service->id = $this->db->query($sql, array($service->name, $service->vendorid, $service->description, $service->text, $service->price));
            if ($service->id)
            {
                return true;
            }
            else
            {
                throw new Exception ($sql.mysql_error());
            }
        }
    }

    public function deleteService(Service &$service)
    {
        if ($service->id) {
            $sql = "DELETE from `services`  WHERE `id`=".$service->id;
            if ($this->db->query($sql))
            {
                return true;
            }
            else
            {
                throw new Exception ($sql);
            }
            $this->deleteServicePictures($service);
        }
    }

    public function saveServicePicture(Service &$service, ServicePicture &$picture, $category)
    {
        if ($service->id>0)
        {

        $sql_npp = "select max(`npp`) from `servicepicture` where `serviceid`=".$service->id." and `category`='".$category."'";
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
            $sql = "INSERT INTO `servicepicture` (`serviceid`, `category`, `npp`, `filename`) VALUES ({?},{?},{?},{?})";
            if ($this->db->query($sql, array($service->id, $category, $npp, basename($picture->path))))
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

    public function getServicePictures(Service &$service)
    {
        $sql = "SELECT * FROM `servicepicture` where `serviceid`=".$service->id;
        $pictures_rows = $this->db->select($sql);

        for ($i=0, $c=count($pictures_rows); $i<$c; $i++)
        {
            $category = $pictures_rows[$i]["category"];
            $npp = $pictures_rows[$i]["npp"];
            $service->pictures["$category"]["$npp"] = new ServicePicture($_SERVER["DOCUMENT_ROOT"]. "/".PICTURE_DIR. "/". $category ."/". $pictures_rows[$i]["filename"]);
        }
    }



    public function deleteServicePictures(Service &$service)
    {
        $sql_pictures_delete = "select * from servicepicture where `serviceid`=".$service->id;
        $pictures_rows = $this->db->select($sql_pictures_delete);

        for ($i = 0; $i < count($pictures_rows); $i++)
        {
            $url = $_SERVER["DOCUMENT_ROOT"]."/".PICTURE_DIR."/".$pictures_rows[$i]['category']."/".$pictures_rows[$i]['filename'];
            unlink($url);
        }

        $sql = "DELETE FROM `servicepicture` WHERE `serviceid`=".$service->id;
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
        $sql_pictures_delete = "select * from servicepicture where `filename`={?}";
        $pictures_rows = $this->db->select($sql_pictures_delete, array($filename));

        for ($i = 0; $i < count($pictures_rows); $i++)
        {
            $url = $_SERVER["DOCUMENT_ROOT"]."/".PICTURE_DIR."/".$pictures_rows[$i]['category']."/".$pictures_rows[$i]['filename'];
            unlink($url);
        }

        $sql = "DELETE FROM `servicepicture` WHERE `filename`={?}";
        if ($this->db->query($sql, array($filename)))
        {
            return true;
        }
        else
        {
            throw new Exception ($sql);
        }

    }






}

