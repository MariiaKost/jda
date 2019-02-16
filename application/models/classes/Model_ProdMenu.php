<?php

class Model_ProdMenu  extends Model
{

    public function get_data(DataBase $db, $catalog_id=0)
    {
        $catalog_mapper = new CatalogMapper($db);

        $path = $catalog_mapper->getPath($catalog_id);
        $path_id = array();
        for ($i=0, $c=count($path); $i<$c; $i++)
        {
            $path_id[$i] = $path[$i]->id;
        }
        $path_id[] = $catalog_id;

        $level1 = $catalog_mapper->getChildrenCatalogs (SERIES_CATALOG_ID);
        for ($i1=0, $c_level1=count($level1); $i1<$c_level1; $i1++)
        {
            if (!in_array($level1[$i1],$path_id))
            {
                $data["menu"]["level1"][$i1] = $catalog_mapper->getCatalog($level1[$i1]);
            }
            else
            {
                $data["menu"]["level1"][$i1]["parent"] = $catalog_mapper->getCatalog($level1[$i1]);
                $level2 = $catalog_mapper->getChildrenCatalogs($level1[$i1]);
                for ($i2=0, $c_level2=count($level2); $i2<$c_level2; $i2++)
                {
                    if (!in_array($level2[$i2],$path_id))
                    {
                        $data["menu"]["level1"][$i1][$i2] = $catalog_mapper->getCatalog($level2[$i2]);
                    }
                    else
                    {
                        $data["menu"]["level1"][$i1][$i2]["parent"] = $catalog_mapper->getCatalog($level2[$i2]);
                        $level3 = $catalog_mapper->getChildrenCatalogs($level2[$i2]);
                        for ($i3=0, $c_level3=count($level3); $i3<$c_level3; $i3++)
                        {
                            $data["menu"]["level1"][$i1][$i2][$i3] = $catalog_mapper->getCatalog($level3[$i3]);
                        }
                    }
                }
            }
        }

        return $data;
    }

} 