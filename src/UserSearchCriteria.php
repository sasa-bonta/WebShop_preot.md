<?php


namespace App;


use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;
use App\Exceptions\NonexistentOrderByColumn;
use App\Exceptions\NonexistentOrderingType;

class UserSearchCriteria
{
    private $param;
    private $page;
    private $limit;
    private $order;
    private $ascDesc;

    /**
     * UserSearchCriteria constructor.
     * @param $param
     * @param $page
     * @param $limit
     * @param $order
     * @param $ascDesc
     */
    public function __construct($param, $page, $limit, $order, $ascDesc)
    {
        $this->param = $param;
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
        if ($order !== 'username' and $order !== 'email') {
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
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param mixed $param
     */
    public function setParam($param): void
    {
        $this->param = $param;
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
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getAscDesc()
    {
        return $this->ascDesc;
    }

    /**
     * @param mixed $ascDesc
     */
    public function setAscDesc($ascDesc): void
    {
        $this->ascDesc = $ascDesc;
    }


}