<?php


namespace App\SearchCriteria;


class OrderSearchCriteria extends SearchCriteria
{
    protected $id;
    protected $status;
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->status = $data['status'] ?? null;
        $this->id = $data['id'] ?? null;
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