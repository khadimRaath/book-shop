<?php

namespace bookshop\src;

use bookshop\includes\DbHelper;

/**
 * Imports data from Json File
 */
class importer
{
    protected $filePath;
    private $dbHelper;
    public function __construct($jsonFilePath)
    {
        $this->filePath = $jsonFilePath;
        $this->dbHelper = new DbHelper();
    }

    /**
     * @return mixed
     */
    public function readJsonFile()
    {
        if (!file_exists($this->filePath)) {
            throw new Exception('Invalid file path.');
        }

        $dataString = file_get_contents($this->filePath);
        $data = json_decode($dataString, true);
        if ($data === null) {
            throw new Exception('Invalid data entries.');
        }

        return $data;
    }

    /**
     * @return true
     * @throws \Exception
     */
    public function saveToDatabase()
    {
        $saleData = $this->readJsonFile();
        foreach ($saleData as $data) {
            $this->dbHelper->saveCustomer($data);
            $this->dbHelper->saveProduct($data);
            $this->dbHelper->saveSale($data);
        }

        return true;
    }
}