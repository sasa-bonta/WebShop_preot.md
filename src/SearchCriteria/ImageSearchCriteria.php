<?php


namespace App\SearchCriteria;


use App\Exceptions\NonexistentOrderByColumn;

class ImageSearchCriteria extends SearchCriteria
{
    const DEFAULT_ORDER = 'id:DESC';

    public function __construct(array $data)
    {
        $data['name'] = $data['search'] ?? null;
        parent::__construct($data);

        if ($this->order !== 'id') {
            throw new NonexistentOrderByColumn("Nonexistent column name");
        }
    }

}