<?php
App::uses('File', 'Utility');

class FileManager
{
    protected $defaultFilePath = WWW_ROOT . DS . 'files' . DS;
    protected $filePath;

    public function __construct($filename = null)
    {
        $this->filename = $filename;
        $this->filePath = $this->defaultFilePath . $this->filename;
    }
}
