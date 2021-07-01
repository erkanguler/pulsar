<?php

namespace Erkan\App;

use PDO;
use Exception;
use function Erkan\App\logError;

class DataGeneration
{

    const UNIT_HOUR = 3600; // Seconds
    const UNIT_DAY = 86400; // Seconds
    const FILE_NAMES = ['countries' => 'countries.json', 'customers' => 'customers.json'];
    const DEVICES = ['iPhone Safari', 'iPad Safari', 'Android Chrome', 'Android Firefox', 'Mac Safari', 'Windows Chrome', 'Windows Firefox'];


    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function generate()
    {
        $countries = $this->getDataFromJSON(self::FILE_NAMES['countries']);
        $customers = $this->getDataFromJSON(self::FILE_NAMES['customers']);
        $tableNames = ['country', 'orders', 'order_item', 'product', 'customer'];

        foreach ($tableNames as $tableName) {
            if (!$this->truncateTable($tableName)) {
                die("Error: Could not reset table \"{$tableName}\".");
            } else {
                //echo "Table \"{$tableName}\" has been reset.<br>";
            }
        }

        if (!$this->insertCountries($countries)) {
            die('Error: Could not populate database with countries.');
        }
        if (!$this->insertCustomers($customers)) {
            die('Error: Could not populate database with customers.');
        }
        if (!$this->insertProducts()) {
            die('Error: Could not populate database with products.');
        }
        if (empty($countries = $this->fetchCountries())) {
            die('Error: Could not fetch countries from database.');
        }
        if (!$this->insertOrders($countries)) {
            die('Error: Could not populate database with orders.');
        }
        if (!$this->insertOrderItems()) {
            die('Error: Could not populate database with order items.');
        }
    }

    private function getDataFromJSON($fileName)
    {
        if (!is_string($json = file_get_contents(dirname(dirname(__FILE__)) . "/{$fileName}"))) {
            die('Could not read json file.');
        }

        if (!$data = json_decode($json, true)) {
            die('Could not parse json.');
        }
        return $data;
    }

    private function insertCountries($countries)
    {
        try {
            $sql = "INSERT INTO `country`(`name`, `code`) VALUES (?,?)";
            $sth = $this->pdo->prepare($sql);

            foreach ($countries as $country) {
                $sth->execute([$country['Name'], $country['Code']]);
            }
            return true;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }

    private function insertCustomers(array $customers)
    {
        try {
            $sql = "INSERT INTO `customer`(`first_name`, `last_name`, `email`) VALUES (?,?,?)";
            $sth = $this->pdo->prepare($sql);

            for ($i = 0; $i < 1000; $i++) {
                $customer = $customers[random_int(0, 9)];
                $sth->execute([$customer['fname'] . $i, $customer['lname'], $i . $customer['email']]);
            }
            return true;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }

    private function insertProducts(): bool
    {
        try {
            $sql = "INSERT INTO `product`(`ean`, `name`, `price`) VALUES (?,?,?)";
            $sth = $this->pdo->prepare($sql);

            for ($i = 1; $i < 101; $i++) {
                $productName = 'Product_' . $i;
                $ean = $productName . '_ean';
                $price = 100 + (5 * $i);

                $sth->execute([$ean, $productName, $price]);
            }
            return true;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }

    private function fetchCountries()
    {
        $countries = [];
        try {
            $sth = $this->pdo->query("SELECT * from country");
            $countries = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $countries;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return $countries;
    }

    private function insertOrders($countries)
    {
        $startTime = 1585699200; // Wed Apr 01 2020 00:00:00 GMT+0000
        $endTime = 1625097600;   // Thu Jul 01 2021 00:00:00 GMT+0000
        try {
            $sql = "INSERT INTO `orders`(`customer_id`, `purchased_ad`, `country_name`, `country_code`, `device`) VALUES (?,?,?,?,?)";
            $sth = $this->pdo->prepare($sql);

            for ($time = $startTime; $time < $endTime; $time += self::UNIT_HOUR) {
                $modulo = $time % self::UNIT_DAY;

                if ($modulo == 0) {
                    continue;
                }

                $customerID = random_int(1, 1000);
                $country = $countries[random_int(0, 248)];
                $sth->execute([$customerID, gmdate("Y-m-d H:i:s", $time), $country['name'], $country['code'], self::DEVICES[random_int(0, 6)]]);
                for ($i = 1; $i < random_int(2, 3); $i++) {
                    $sth->execute([$customerID, gmdate("Y-m-d H:i:s", $time - (self::UNIT_HOUR * $i - random_int(1, 59))), $country['name'], $country['code'], self::DEVICES[random_int(0, 6)]]);
                }
            }
            return true;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }

    private function insertOrderItems()
    {
        try {
            $sql = "INSERT INTO `order_item`(`order_id`, `ean`, `quantity`, `price`) VALUES (?,?,?,?)";
            $sth = $this->pdo->prepare($sql);
            $products = $this->fetchProducts();
            $numOfOrders = $this->fetchNumOfOrders();
            $numOfOrders++;

            for ($orderID = 1; $orderID < $numOfOrders; $orderID++) {

                $numOfProductsInOrder = random_int(2, 4);
                for ($i = 1; $i < $numOfProductsInOrder; $i++) {
                    $randomProduct = random_int(0, 99);
                    $ean = $products[$randomProduct]['ean'];
                    $price = $products[$randomProduct]['price'];
                    $quantity = random_int(1, 3);

                    $sth->execute([$orderID, $ean, $quantity, $quantity * $price]);
                }
            }
            return true;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }

    private function fetchNumOfOrders()
    {
        try {
            $stm = $this->pdo->query("SELECT COUNT(*) AS row_count FROM `orders`");
            $res = $stm->fetch(PDO::FETCH_ASSOC);

            if (is_array($res)) {
                return intval($res['row_count']);
            }

            throw new Exception('Some error occured.');
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }

    private function fetchProducts()
    {
        $products = [];
        try {
            $stm = $this->pdo->query("SELECT * FROM `product`");
            $products = $stm->fetchAll(PDO::FETCH_ASSOC);

            return $products;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return $products;
    }

    private function truncateTable($table)
    {
        $tables = ['customer', 'orders', 'product', 'order_item'];
        try {
            if (in_array($table, $tables)) {
                $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            }
            $this->pdo->exec("TRUNCATE TABLE {$table}");
            if (in_array($table, $tables)) {
                $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            }
            return true;
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
        }
        return false;
    }
}
