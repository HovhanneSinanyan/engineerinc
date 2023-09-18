<?php

namespace Kernel;

class Validator {
    private $data;
    private $errors = [];

    public function __construct($data) {
        $this->data = $data;
    }

    public function validateRequired($field, $message = null) {
        $message = $message ?? "The {$field} is required";
        if (empty($this->data[$field])) {
            $this->errors[$field][] = $message;
        }
    }

    public function validateEmail($field, $message = null) {
        $message = $message ?? "Invalid email format";
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message;
        }
    }

    public function validateNumeric($field, $message = null) {
        $message = $message ?? "The {$field} must be a number";
        if (!is_numeric($this->data[$field])) {
            $this->errors[$field][] = $message;
        }
    }

    public function validateStringMax($field, $max, $message = null) {
        $message = $message ?? "The {$field} must contain up to {$max} symbols";
        if (strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = $message;
        }
    }

    public function validateStringMin($field, $min, $message = null) {
        $message = $message ?? "The {$field} must contain at least {$min} symbols";
        if (strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = $message;
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function isValid() {
        return empty($this->errors);
    }
}