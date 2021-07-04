<?php

namespace Erkan\App\Controller;

use Erkan\App\DataGeneration;
use Erkan\App\DB;
use Erkan\App\Kernel\AbstractController;
use stdClass;

class DataGenerationController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->render('dataGeneration', ['h1' => 'Data generation', 'title' => 'Data generation']);
    }

    public function generatedata()
    {
        $db = new DB();
        $dg = new DataGeneration($db->getPDO());
        $dg->generate();

        $data = new stdClass();
        $data->message = 'Data generation finnished.';
        return $this->renderJSON($data);
    }
}
