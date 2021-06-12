<?php


namespace App\SearchCriteria;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageSearchCriteria extends SearchCriteria
{
    const DEFAULT_ORDER = 'id:DESC';

    public function __construct(array $data)
    {
        parent::__construct($data);

        if ($this->order !== 'id') {
            throw new BadRequestHttpException("Nonexistent column name");
        }
    }

}