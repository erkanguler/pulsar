<?php

namespace Erkan\App\Exception;

use Exception;

class InternalServerErrorException extends Exception
{

    public function __construct($msg = 'Internal server error')
    {
        parent::__construct($msg);
    }
}
