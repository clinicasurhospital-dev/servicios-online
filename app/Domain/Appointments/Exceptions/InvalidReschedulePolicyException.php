<?php

namespace App\Domain\Appointments\Exceptions;

use Exception;

class InvalidReschedulePolicyException extends Exception
{
    protected $message = 'No es posible reprogramar la cita debido a la política de anticipación mínima requerida.';
}
