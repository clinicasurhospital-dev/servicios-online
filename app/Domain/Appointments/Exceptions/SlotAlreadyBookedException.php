<?php

namespace App\Domain\Appointments\Exceptions;

use Exception;

class SlotAlreadyBookedException extends Exception
{
    protected $message = 'El horario seleccionado ya ha sido reservado por otro paciente. Por favor selecciona otro turno disponible.';
}
