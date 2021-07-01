<?php

namespace Erkan\App;

use Erkan\App\Exception\InternalServerErrorException;

if (!\function_exists(isDateOrDatesValid::class)) {
    /**
     *  Checks format and validity of passed date or dates 
     *  ISO 8601    yy-mm-dd
     * 
     *  @param string date|dates
     *  @throws InternalServerErrorException 
     */
    function isDateOrDatesValid(): bool
    {
        if (empty($dates = func_get_args())) {
            $errMsg = 'Function is used with no argument.';
            logError(__LINE__, __FILE__, $errMsg);
            throw new InternalServerErrorException($errMsg);
        }

        $isTypeCorrect = true;
        foreach ($dates as $date) {
            $isTypeCorrect &= is_string($date);
        }

        if (!$isTypeCorrect) {
            $errMsg = 'Function is used with wrong argument type.';
            logError(__LINE__, __FILE__, $errMsg);
            throw new InternalServerErrorException($errMsg);
        }

        $result = true;
        foreach ($dates as $date) {
            $result &= preg_match('%^(\d{4})-(\d\d)-(\d\d)$%i', $date, $match);

            if (!empty($match)) {
                $year = $match[1];
                $month = $match[2];
                $day = $match[3];
            }

            if ($result) {
                $result = checkdate($month, $day, $year);
            }
        }
        return $result;
    }
}

if (!\function_exists(logError::class)) {
    function logError($line, $file, $msg)
    {
        error_log('Line ' . $line . ' in file ' . $file . ' ' . $msg);
    }
}
