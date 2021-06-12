<?php


namespace App\SearchCriteria;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductAdminSearchCriteria extends SearchCriteria
{
    private $category;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->category = $data['category'] ?? null;

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