<?php
require_once 'includes/config.php';
require_once 'includes/dBHelper.php';
require_once 'includes/versionCompare.php';
require_once 'src/importer.php';
require_once 'src/search.php';

use bookshop\src\importer;
use bookshop\src\search;

try {
    if(isset($_GET['import'])) {
        $importer = new importer('data/DEV_Sales_full.json');
        $isImported = $importer->saveToDatabase();
        if ($isImported) {
            echo 'Data imported successfully. <br>';
            echo 'Please <a href="?search">search now</a>';
        }
    } else {
        $search = new search();
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
        $search->showResults($searchQuery);
    }
}catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
