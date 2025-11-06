<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentYear extends Constraint
{
    public $message = 'El año de publicación no puede ser mayor al año actual ({{ current_year }})';
}
