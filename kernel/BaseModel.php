<?php

namespace Kernel;

class BaseModel extends Builder {

    protected $timestamps = true;
    public function __get($param)
    {
        return $this->fields[$param];
    }

    public function __set($param, $value)
    {
        $this->fields[$param] = $value;
    }



}