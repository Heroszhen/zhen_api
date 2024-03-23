<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;


/**
 * @Annotations
 * @Target({"CLASS", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CheckEmail extends Constraint
{
    public $message = 'The value "{{ value }}" is not valid.';

    public function validatedBy()
    {
        return static::class . 'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
