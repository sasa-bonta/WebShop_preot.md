<?php


namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class NonexistentOrderingType extends BadRequestHttpException
{
    public function __construct(?string $message = '', \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);
    }
}