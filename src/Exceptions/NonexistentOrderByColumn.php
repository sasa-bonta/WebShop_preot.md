<?php


namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class NonexistentOrderByColumn extends BadRequestHttpException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}