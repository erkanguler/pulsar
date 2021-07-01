<?php

namespace Erkan\App\Exception;

use Exception;

class NotFoundException extends Exception
{

    public function __construct($msg = 'Not Found')
    {
        parent::__construct($msg);
    }
}
