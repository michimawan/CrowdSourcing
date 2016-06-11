<?php
App::uses('File', 'Utility');

class FileManager
{
    protected $defaultFilePath = WWW_ROOT . 'files' . DS;
    protected $filePath;

    protected function __construct($filename = null)
    {
        $this->filename = $filename;
        $this->filePath = $this->defaultFilePath . $this->filename;
    }
}
