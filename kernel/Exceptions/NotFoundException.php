<?php

namespace Kernel\Exceptions;

use Exception;

class NotFoundException extends Exception {

    public function __construct() {
        parent::__construct("", 404);
    }
}