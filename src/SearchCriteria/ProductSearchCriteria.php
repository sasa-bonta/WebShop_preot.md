<?php


namespace App\SearchCriteria;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductSearchCriteria extends SearchCriteria
{
    private $category;
    const DEFAULT_LIMIT = 16;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->category = $data['category'] ?? null;

        if(!in_array($this->limit, [16, 32, 64, 128])) {
            throw new BadRequestHttpException("The limit is not in array");
        }

        if ($this->order !== 'created_at' && $this->order !== 'price') {
            throw new BadRequestHttpException("Nonexistent column name");
        }
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category): void
    {
        $this->category = $category;
    }
}