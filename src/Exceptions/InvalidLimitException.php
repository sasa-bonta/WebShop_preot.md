<?php


namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class InvalidLimitException extends BadRequestHttpException
{

}