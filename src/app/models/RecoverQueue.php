<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Messages\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class RecoverQueue extends Model
{
    public function validation()
    {
        $validator = new Validation();
     

        if ($this->validationHasFailed() === true) {
            return false;
        }
    }
}