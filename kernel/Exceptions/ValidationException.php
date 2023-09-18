<?php

namespace Kernel\Exceptions;

use Exception;

class ValidationException extends Exception {

    public function __construct(array $errors) {
        $errors = json_encode($errors);
        parent::__construct($errors, 422);
    }
}