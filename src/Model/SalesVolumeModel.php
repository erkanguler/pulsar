<?php

namespace Erkan\App\Model;

use PDO;
use Exception;
use Erkan\App\Exception\InternalServerErrorException;
use function Erkan\App\logError;

class SalesVolumeModel
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws InternalServerErrorException 
     */
    function getData(string $fromDate, string $toDate): array
    {
        $data = [];
        try {
            $sql = "
            SELECT
                CONCAT(DATE_FORMAT(`purchased_ad`, '%y'), ' ',DATE_FORMAT(`purchased_ad`, '%b'), ' ', DAYOFMONTH(`purchased_ad`)) AS date,
                COUNT(DISTINCT `customer_id`) as unique_customers,
                COUNT(DISTINCT order_id) as num_of_orders,
                SUM(price) as revenue
            FROM order_item oi, orders o
            WHERE oi.order_id = o.id
            AND `purchased_ad` BETWEEN '{$fromDate}' and DATE_ADD('{$toDate}', INTERVAL 1 DAY)
            GROUP BY YEAR(purchased_ad), MONTH(purchased_ad), DAY(purchased_ad)";

            $stm = $this->pdo->query($sql);
            $data = $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            logError(__LINE__, __FILE__, $e->getMessage());
            throw new InternalServerErrorException();
        }
        return $data;
    }
}
