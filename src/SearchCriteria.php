<?php


namespace App;


use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;
use App\Exceptions\NonexistentOrderByColumn;
use App\Exceptions\NonexistentOrderingType;
use Exception;

class SearchCriteria
{
    private $name;
    private $category;
    private $page;
    private $limit;
    private $order;
    private $ascDesc;

    /**
     * SearchCriteria constructor.
     * @throws Exception
     */
    public function __construct($name, $category, $page, $limit, $order, $ascDesc)
    {

        if ($page <= 0) {
            throw new InvalidPageException("Page must be positive");
        }
        if ($limit <= 0) {
            throw new InvalidLimitException("Limit must be positive");
        }
        if ($limit > 128) {
            throw new InvalidLimitException("Limit must be <= 128");
        }
        if ($order !== 'created_at' && $order !== 'price') {
            throw new NonexistentOrderByColumn("Nonexistent column name");
        }

        if ($ascDesc !== 'ASC' && $ascDesc !== 'DESC') {
            throw new NonexistentOrderingType("Nonexistent sort order");
        }
        $this->ascDesc = $ascDesc;
        $this->name = $name;
        $this->category = $category;
        $this->page = $page;
        $this->limit = $limit;
        $this->order = $order;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;
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