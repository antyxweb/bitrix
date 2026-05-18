<?php

namespace Norbit\Appointment\Exception;

class NorbitAppointmentException extends \Exception implements BaseExceptionInterface
{
    public function __construct(?string $message, \Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}