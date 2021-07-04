<?php

namespace Erkan\App\Controller;

use Erkan\App\Kernel\AbstractController;

class IndexController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->render('index', ['welcome' => 'Welcome to our website!' ,'title' => 'Home']);
    }
}
