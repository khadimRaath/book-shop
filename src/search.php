<?php

namespace bookshop\src;

use bookshop\includes\DbHelper;

/**
 * Implements search from sales.
 */
class search
{
    private $dbHelper;

    /**
     * Constructs class
     */
    public function __construct()
    {
        $this->dbHelper = new DbHelper();
    }

    /**
     * @param $searchQuery
     * @return void
     */
    public function showResults($searchQuery)
    {
        ob_start();
        $results = $this->dbHelper->searchProducts($searchQuery);
        include 'includes/searchResults.php';
    }
}