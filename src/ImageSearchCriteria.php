<?php


namespace App;


use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;
use App\Exceptions\NonexistentOrderByColumn;
use App\Exceptions\NonexistentOrderingType;

class ImageSearchCriteria
{
    private $tag;
    private $page;
    private $limit;
    private $order;
    private $ascDesc;

    /**
     * ImageSearchCriteria constructor.
     * @param $tag
     * @param $page
     * @param $limit
     * @param $order
     * @param $ascDesc
     */
    public function __construct($tag, $page, $limit, $order, $ascDesc)
    {
        $this->tag = $tag;
        if ($page <= 0) {
            throw new InvalidPageException("Page must be positive");
        } else {
            $this->page = $page;
        }
        if ($limit <= 0) {
            throw new InvalidLimitException("Limit must be positive");
        } else {
            $this->limit = $limit;
        }
        if ($order !== 'id') {
            throw new NonexistentOrderByColumn("Nonexistent column name");
        } else {
            $this->order = $order;
        }
        if ($ascDesc !== 'ASC' and $ascDesc !== 'DESC') {
            throw new NonexistentOrderingType("Nonexistent sort order");
        } else {
            $this->ascDesc = $ascDesc;
        }
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag): void
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page): void
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getAscDesc(): string
    {
        return $this->ascDesc;
    }

    /**
     * @param string $ascDesc
     */
    public function setAscDesc(string $ascDesc): void
    {
        $this->ascDesc = $ascDesc;
    }
}