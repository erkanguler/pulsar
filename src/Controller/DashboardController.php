<?php

namespace Erkan\App\Controller;

use stdClass;
use Erkan\App\DB;
use Erkan\App\Kernel\Request;
use Erkan\App\Kernel\AbstractController;
use Erkan\App\Model\SalesVolumeModel;
use Erkan\App\Exception\InternalServerErrorException;
use function Erkan\App\isDateOrDatesValid;

class DashboardController extends AbstractController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard()
    {
        $req = new Request();
        if ($req->getMethod() == 'POST') {
            $fromDate = $_POST['fromDate'];
            $toDate = $_POST['toDate'];

            return $this->getSalesVolumeData($fromDate, $toDate);
        }

        return $this->render('dashboard');
    }

    private function getSalesVolumeData($fromDate, $toDate)
    {
        $db = new DB();
        $salesVolume = new SalesVolumeModel($db->getPDO());
        $e = new stdClass();
        $e->error = '';

        try {
            if (!isDateOrDatesValid($fromDate, $toDate)) {
                $e->error = 'Please, provide valid dates!';
                return $this->renderJSON($e);
            }

            $allData = $salesVolume->getData($fromDate, $toDate);
            return $this->renderJSON($this->formatData($allData));
        } catch (InternalServerErrorException $exc) {
            $e->error = $exc->getMessage();
            return $this->renderJSON($e, 500);
        }
    }

    private function formatData(array $allData): stdClass
    {
        $data = new stdClass();
        $data->dates = [];
        $data->numOfOrders = [];
        $data->numOfUniqueCustomers = [];
        $data->revenues = [];

        foreach ($allData as $d) {
            $data->dates[] = $d['date'];
            $data->numOfOrders[] = intval($d['num_of_orders']);
            $data->numOfUniqueCustomers[] = intval($d['unique_customers']);
            $data->revenues[] = floatval($d['revenue']);
        }
        return $data;
    }
}
