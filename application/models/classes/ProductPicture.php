<?php

class ProductPicture {

    //const PICTURE_DIR = "productpictures";
    const JPEG_QUALITY = 80;
    
    private $path;

    public function __construct ($path='')
    {
        $this->path = $path;
    }

    public function __get($attr)
    {
        if (isset($this->$attr))
            return $this->$attr;
        else
            die("Атрибут ".$attr." не найден!");
    }


    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }

    function getFileName ()
    {
        return basename($this->path);
    }

    function getFileBaseName ()
    {
        $actual_image_name = $this->getFileName();
        return mb_substr($actual_image_name, 0, mb_strrpos($actual_image_name,".",0, "UTF-8"), "UTF-8");;
    }


    function getCategory ()
    {
        $a = explode("/", $this->path);
        $count = count($a);
        return $a[$count-2];
    }

    public static function saveImage ($image, $path, $file_type)
    {
        switch ($file_type)
        {
            case 2:
                imagejpeg($image, $path, self::JPEG_QUALITY);
                break;

            case 3:
                imagepng($image, $path);
                break;

            case 1:
                imagegif($image, $path);
                break;
        }

    }

// Имя файла остается такое же, как у передаваемого $product_picture_file. Картинка записывается с тем же именем в директорию, соответствующую названию категории (large, small).
// Название категории передается как параметр
// Ширина и высота фиксированы
    /**
     * @param $product_picture_file
     * @param $picture_width
     * @param $category
     * @param string $picture_height
     * @param bool $fixed_width
     * @return string
     */
    public static function create ($product_picture_file, $category, $picture_width, $picture_height)
    {
        if (!empty($product_picture_file))
        {
            list($width, $height, $file_type) = getimagesize($product_picture_file);

            switch ($file_type)
            {
                case 2:
                    $source = imagecreatefromjpeg($product_picture_file);
                    break;

                case 3:
                    $source = imagecreatefrompng($product_picture_file);
                    break;

                case 1:
                    $source = imagecreatefromgif($product_picture_file);
                    break;
            }

            //Оставляем небольшие поля по краям (5% от длины стороны)
            $picture_width1 = $picture_width-$picture_width*0.05;
            $picture_height1 = $picture_height-$picture_height*0.05;

            $picture_ratio = $picture_width1 / $width;

            if ($height * $picture_ratio > $picture_height1)
            {
                $picture_ratio = $picture_height1 / $height;
            }

            $dir_path = $_SERVER["DOCUMENT_ROOT"]."/".PICTURE_DIR."/".$category."/";
            $path = $dir_path . basename($product_picture_file);

            if (!is_dir ($dir_path))
            {
               mkdir ($dir_path, 0777);
               //chmod ($dir_path, 0775);
            }

            $image = imagecreatetruecolor($picture_width, $picture_height);
            imageAlphaBlending($image, false);
            imageSaveAlpha($image, true);
            $background_color = imagecolorallocate($image, 255, 255, 255);
            imagefill($image,0,0, $background_color);

            $w = ($picture_ratio < 1 ? $width * $picture_ratio : $width);
            $h = ($picture_ratio < 1 ? $height * $picture_ratio : $height);
            imagecopyresampled($image, $source, round(($picture_width-$w)/2), round(($picture_height-$h)/2), 0, 0, $w, $h, $width, $height);
            self::saveImage ($image, $path, $file_type);
            imagedestroy($image);
            //chmod($path, 0644);


        }
        else $path="";

        return $path;
    }


    //Пока не используется, возможно, пригодится в дальнейшем
    // Имя файла генерируется внутри класса имя передаваемого $product_picture_file не используется (предполагается, что оно временное). Категория не учитывается: предполагается, что картинки пишутся в общую папку с указанными размерами, а категория для картинки устанавливается уже только при сохранении в базу.
    // Ширина фиксирована в зависимости от параметра $fixed_width. Высоту пожно ограничить ( но не зафиксировать), указав параметр $picture_height.
    public static function create_backup ($product_picture_file, $picture_width, $picture_height="", $fixed_width=false)
    {
        if (!empty($product_picture_file))
        {
                list($width, $height, $file_type) = getimagesize($product_picture_file);

                switch ($file_type)
                {
                    case 2:
                        $source = imagecreatefromjpeg($product_picture_file);
                        $ext = ".jpg";
                        break;

                    case 3:
                        $source = imagecreatefrompng($product_picture_file);
                        $ext = ".png";
                        break;

                    case 1:
                        $source = imagecreatefromgif($product_picture_file);
                        $ext = ".gif";
                        break;
                }

                $picture_ratio = $picture_width / $width;

                if (($picture_height>0)&&($height * $picture_ratio > $picture_height))
                {
                    $picture_ratio = $picture_height / $height;
                }

                $path = $_SERVER["DOCUMENT_ROOT"].PICTURE_DIR."/" . uniqid(rand(100, 999)) . $ext;

                if ($picture_ratio < 1)
                {
                    $image = imagecreatetruecolor($w = $width * $picture_ratio, $h = $height * $picture_ratio);
                    imagecopyresampled($image, $source, 0, 0, 0, 0, $w, $h, $width, $height);

                    if ($fixed_width && ($w < $picture_width))
                    {
                        $image_new = imagecreatetruecolor($w1 = $picture_width, $h1 = $h);
                        imageAlphaBlending($image, false);
                        $background_color = imagecolorallocate($image_new, 255, 255, 255);
                        imagefill($image_new,0,0, $background_color);

                        imagecopy($image_new, $image, round(($w1-$w)/2), 0, 0, 0, $w, $h);

                        self::saveImage ($image_new, $path, $file_type);

                        imagedestroy($image_new);
                    }
                    else
                    {
                        self::saveImage ($image, $path, $file_type);
                    }
                    imagedestroy($image);
                }

                elseif ($picture_ratio ==1)
                {
                    self::saveImage ($source, $path, $file_type);
                }

                else
                {
                    if ($fixed_width)
                    {
                        $image = imagecreatetruecolor($w = $picture_width, $h = $height);
                        imageAlphaBlending($source, false);
                        $background_color = imagecolorallocate($image, 255, 255, 255);
                        imagefill($image,0,0, $background_color);
                        imagecopy($image, $source, round(($w-$width)/2), round(($h-$height)/2), 0, 0, $width, $height);

                        self::saveImage ($image, $path, $file_type);
                        imagedestroy($image);
                    }
                    else
                    {
                        self::saveImage ($source, $path, $file_type);
                    }
                }

                //chmod($path, 0644);

        }
        else $path="";

        return $path;
    }

}