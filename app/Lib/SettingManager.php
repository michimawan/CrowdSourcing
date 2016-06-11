<?php
App::uses('File', 'Utility');
App::import('Lib', 'FileManager');

class SettingManager extends FileManager
{
    private static $settingObject;

    protected function __construct($filename)
    {
        parent::__construct($filename);

        $this->file = new File($this->filePath);
        $this->readFile();
    }

    public static function getSettingObject($filename)
    {
        if (static::$settingObject == null) {
            static::$settingObject = new SettingManager($filename);
        }

        return static::$settingObject;
    }

    private function __clone() {}
    private function __wakeup() {}

    public function getData()
    {
        return $this->data;
    }

    public function getLock()
    {
        return $this->data->lock;
    }

    public function getPrice()
    {
        return $this->data->price;
    }

    public function getN()
    {
        return $this->data->n;
    }

    public function setData($data = [])
    {
        if(count($data) == 3) {
            $toWrite = json_encode($data);
            file_put_contents($this->filePath, $toWrite);
        }
    }

    public function setLock($data = [])
    {
        if(count($data) == 1) {
            $data['n'] = $this->getN();
            $data['price'] = $this->getPrice();

            $toWrite = json_encode($data);
            file_put_contents($this->filePath, $toWrite);
        }
    }

    public function setPrice($data = [])
    {
        if(count($data) == 1) {
            $data['n'] = $this->getN();
            $data['lock'] = $this->getLock();

            $toWrite = json_encode($data);
            file_put_contents($this->filePath, $toWrite);
        }
    }

    public function setN($data = [])
    {
        if(count($data) == 1) {
            $data['lock'] = $this->getLock();
            $data['price'] = $this->getPrice();

            $toWrite = json_encode($data);
            file_put_contents($this->filePath, $toWrite);
        }
    }

    private function readFile()
    {
        $json = $this->file->read(true, 'r');
        $this->data = json_decode($json);
    }
}
