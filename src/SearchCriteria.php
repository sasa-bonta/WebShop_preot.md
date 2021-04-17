<?php


namespace App;


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
        $this->name = $name;
        $this->category = $category;
        if ($page <= 0) {
            throw new Exception("Page must be positive");
        } else {
            $this->page = $page;
        }
        if ($limit <= 0) {
            throw new Exception("Limit must be positive");
        } else {
            $this->limit = $limit;
        }
        if ($order !== 'created_at' and $order !== 'price') {
            throw new Exception("Nonexistent column name");
        } else {
            $this->order = $order;
        }
        if ($ascDesc !== 'ASC' and $ascDesc !== 'DESC') {
            throw new Exception("Nonexistent sort order");
        } else {
            $this->ascDesc = $ascDesc;
        }
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