<?php


namespace App\SearchCriteria;

use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;
use App\Exceptions\NonexistentOrderingType;
use Exception;

class SearchCriteria
{
    protected $id;
    protected $name;
    protected $status;
    protected $page;
    protected $limit;
    protected $order;
    protected $ascDesc;
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 10;
    const DEFAULT_ORDER = 'created_at:DESC';

    /**
     * SearchCriteria constructor.
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->page = $data['page'] ?? static::DEFAULT_PAGE;
        $this->limit = $data['limit'] ?? static::DEFAULT_LIMIT;
        $order = $data['order'] ?? static::DEFAULT_ORDER;
        list($this->order, $this->ascDesc) = explode(":", $order, 2);

        if ($this->page <= 0) {
            throw new InvalidPageException("The page must be greater than zero");
        }

        if ($this->limit <= 0 || $this->limit > 128) {
            throw new InvalidLimitException("The limit must be: 0 < limit < 129");
        }

        if ($this->ascDesc !== "ASC" && $this->ascDesc !== "DESC") {
            throw new NonexistentOrderingType("Ordering type must be ASC or DESC");
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage(int $page)
    {
        $this->page = $page;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getAscDesc()
    {
        return $this->ascDesc;
    }
}