<?php

namespace Kernel\Exceptions;

use Exception;

class AuthException extends Exception {

    public function __construct() {
        parent::__construct("", 401);
    }
}