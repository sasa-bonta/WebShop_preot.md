<?php


namespace App\SearchCriteria;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderSearchCriteria extends SearchCriteria
{
    protected $id;
    protected $status;
    const DEFAULT_ORDER = 'created_at:DESC';

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->status = $data['status'] ?? null;
        $this->id = $data['id'] ?? null;
        if ($this->order !== 'created_at') {
            throw new BadRequestHttpException("Nonexistent column name");
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }
}