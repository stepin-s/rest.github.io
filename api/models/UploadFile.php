<?php

require_once __DIR__ . '/../_params.php';

/**
 * Class UploadFile. Класс для загрузки файла
 */
class UploadFile
{
    protected $name;
    protected $dir;
    protected $attribute;
    protected $maxWidth = 1000;
    protected $maxHeight = 1000;
    protected $_errors = [];
    protected $_types = [];

    const ATTRIBUTE = 'file';

    const MIME_PNG = 'image/png';
    const MIME_JPEG = 'image/jpeg';
    const MIME_GIF = 'image/gif';
    const MIME_BMP = 'image/bmp';

    public function __construct($attribute = '', $dir = '')
    {
        $this->attribute = !empty($attribute) ? $attribute : self::ATTRIBUTE;
        $this->dir       = $GLOBALS['params']['tmp'];
        $this->_types    = $this->getTypeList();
    }


    public function getAttribute()
    {
        return $this->attribute;
    }

    public function setAttribute($value)
    {
        $this->attribute = $value;
    }

    public function getDir()
    {
        return $this->dir;
    }

    public function setDir($value)
    {
        $this->dir = $value;
    }

    public function getName()
    {
        return (string)$this->name;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Установить список разрешенных типов файла
     *
     * @param array $value
     */
    public function setMimeType($value = [])
    {
        $value = (array)$value;
        if (empty($value)) {
            $this->_types = $this->getTypeList();
        } else {
            $this->_types = array_intersect($value, $this->getTypeList());
        }
    }

    /**
     * Список всех поддерживаемых типов файла
     *
     * @return array
     */
    protected function getTypeList()
    {
        return [
            self::MIME_JPEG,
            self::MIME_PNG,
            self::MIME_BMP,
            self::MIME_GIF,
        ];
    }


    public function generateFileName()
    {
		if (!is_dir($this->dir))
			return null;
		
        $files = scandir($this->dir);

        do {
            $randStr = mt_rand(10000000, 999999999);
        } while (in_array($randStr, $files, false));

        return $randStr;
    }

    /**
     * Загрузка файла
     */
    public function load()
    {
        $attr = $this->attribute;

        if (!empty($_FILES[$attr])) {
            $filePath = $_FILES[$attr]['tmp_name'];
            $fileMime = $_FILES[$attr]['type'];

            if (!in_array($fileMime, $this->_types, false)) {
                $this->_errors['_load'] = 'Incorrect MIME type';
            } else {
                $this->name = $this->generateFileName();
                $image      = new Imagick($filePath);
                $image      = $this->resize($image);
                $image->writeImage($this->dir . '/' . $this->name);
                $image->clear();
            }
        } else {
            $this->_errors['_load'] = 'Missing file';
        }
    }

    public function resize(Imagick $image)
    {
        $w = $image->getImageWidth();
        $h = $image->getImageHeight();

        $scale = 1;

        if ($w / $this->maxWidth > $scale) {
            $scale = $w / $this->maxWidth;
        }

        if ($h / $this->maxHeight > $scale) {
            $scale = $h / $this->maxHeight;
        }

        if ($scale > 1) {
            $image->thumbnailImage($w / $scale, $h / $scale);
        }

        return $image;
    }

    //    public function save
}
