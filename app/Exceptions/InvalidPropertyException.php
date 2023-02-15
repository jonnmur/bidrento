<?php
 
namespace App\Exceptions;
 
use Exception;
 
class InvalidPropertyException extends Exception
{
    private $errors = [];
    private $status;

    public function __construct($errors, $status) {
        $this->errors = $errors;
        $this->status = $status;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getStatus() {
        return $this->status;
    }
}