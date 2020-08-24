<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NoBadWords extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Le mot "merde" est interdit !! ';

    public $forbiddenWords = [
        'merde', 'con', '...'
    ];
}
