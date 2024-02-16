<?php

namespace bookshop\includes;

use data\Exception;

/**
 * Implement Database operations.
 */
class DbHelper
{
    private $dbConnection;
    const CUSTOMERS_TABLE = 'customers';
    const PRODUCTS_TABLE = 'products';
    const SALES_TABLE = 'sales';
    const SALE_ITEMS_TABLE = 'sale_items';

    /**
     * @throws \Exception
     */
    function __construct()
    {
        $this->dbConnection = new \mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if ($this->dbConnection->connect_error) {
            throw new \Exception("Connection failed: " . $this->dbConnection->connect_error);
        }

    }

    /**
     * @param $customer
     * @return bool|\mysqli_result
     */
    function saveCustomer($customer)
    {
        $name = $this->sanitizeString($customer['customer_name']);
        $email = $this->sanitizeString($customer['customer_mail']);
        $table = self::CUSTOMERS_TABLE;
        $query = "INSERT INTO {$table} (name, email) VALUES ('{$name}', '{$email}')";
        $query .= " ON DUPLICATE KEY UPDATE name = '{$name}';";

        return $this->dbConnection->query($query);
    }

    /**
     * @param $email
     * @return array|false|null
     */
    function findCustomerByEmail($email)
    {
        $email = $this->sanitizeString($email);
        $table = self::CUSTOMERS_TABLE;
        $query = "SELECT * FROM {$table} where email = '{$email}'";

        return $this->dbConnection->query($query)->fetch_assoc();
    }

    /**
     * @param $product
     * @return bool|\mysqli_result
     */
    function saveProduct($product)
    {
        $productId = filter_var($product['product_id'], FILTER_SANITIZE_NUMBER_INT);
        $productName = $this->sanitizeString($product['product_name']);
        $productPrice = doubleval($product['product_price']);
        $table = self::PRODUCTS_TABLE;
        $query = "INSERT INTO {$table} (id, name, price) VALUES ({$productId}, '{$productName}', {$productPrice})";
        $query .= " ON DUPLICATE KEY UPDATE name = '{$productName}', price = {$productPrice};";

        return $this->dbConnection->query($query);
    }

    /**
     * @param $saleId
     * @param $productId
     * @param $productPrice
     * @return bool|\mysqli_result
     */
    function saveSaleItem($saleId, $productId, $productPrice)
    {
        $table = self::SALE_ITEMS_TABLE;
        $query = "INSERT INTO {$table} (sale_id, product_id, product_price) VALUES ('{$saleId}', '{$productId}', {$productPrice})";
        $query .= " ON DUPLICATE KEY UPDATE product_price = {$productPrice};";

        return $this->dbConnection->query($query);
    }


    /**
     * @param $sale
     * @return void
     * @throws \Exception
     */
    function saveSale($sale)
    {
        $vc = new VersionCompare();

        $customer = $this->findCustomerByEmail($sale['customer_mail']);

        $id = $this->sanitizeString($sale['sale_id']);

        $saleDate = $this->sanitizeString($sale['sale_date']);
        $version = $this->sanitizeString($sale['version']);
        $date = new \DateTime($saleDate, new \DateTimeZone($vc->getTimeZoneByVersion($version)));
        $formatedDate = date_format($date, 'Y-m-d H:i:s');

        $table = self::SALES_TABLE;
        $query = "INSERT INTO {$table} (id, customer_id, sale_date, version) VALUES ('{$id}', {$customer['id']}, '{$formatedDate}', '{$version}')";
        $query .= " ON DUPLICATE KEY UPDATE customer_id = {$customer['id']}, sale_date = '{$formatedDate}', version = '{$version}';";

        if ($this->dbConnection->query($query)) {
            $productId = filter_var($sale['product_id'], FILTER_SANITIZE_NUMBER_INT);
            $productPrice = doubleval($sale['product_price']);
            $this->saveSaleItem($id, $productId, $productPrice);
        }
    }

    /**
     * @param $str
     * @return string
     */
    private function sanitizeString($str)
    {
        return mysqli_real_escape_string($this->dbConnection, $str);
    }


    /**
     * @param $searchQuery
     * @return array
     */
    public function searchProducts($searchQuery = '')
    {
        $searchQuery = $this->sanitizeString($searchQuery);

        $salesTable = self::SALES_TABLE;
        $customerTable = self::CUSTOMERS_TABLE;
        $productTable = self::PRODUCTS_TABLE;
        $saleItemsTable = self::SALE_ITEMS_TABLE;

        $query = "SELECT $salesTable.id as sale_id, $salesTable.sale_date as sale_date, $customerTable.name as customer_name, $customerTable.email as customer_email, $productTable.name as product_name, $saleItemsTable.product_price "
            . " FROM {$salesTable} "
            . " Join $saleItemsTable on $saleItemsTable.sale_id = $salesTable.id"
            . " Join $productTable on $productTable.id = $saleItemsTable.product_id"
            . " Join $customerTable on $customerTable.id = $salesTable.customer_id"
            . " WHERE $customerTable.name LIKE '%{$searchQuery}%' OR $customerTable.email LIKE '%{$searchQuery}%'"
            . " OR $productTable.name LIKE '%{$searchQuery}%' "
            . " OR $saleItemsTable.product_price LIKE '%{$searchQuery}%'";

        $rows = mysqli_query($this->dbConnection,$query);

        return $rows->fetch_all(1);
    }
}