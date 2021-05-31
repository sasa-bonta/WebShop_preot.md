<?php


namespace App\SearchCriteria;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserSearchCriteria extends SearchCriteria
{
    const DEFAULT_ORDER = 'email:ASC';

    public function __construct(array $data)
    {
        parent::__construct($data);

        if ($this->order !== 'username' && $this->order !== 'email') {
            throw new BadRequestHttpException("Nonexistent column name");
        }
    }

}