<?php

namespace Hulotte\Services;

/**
 * Class Paginator
 *
 * @package Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Paginator
{
    /**
     * @var int
     */
    public $currentPage;
    
    /**
     * @var int
     */
    public $totalPages;

    /**
     * @var array
     */
    private $records;

    /**
     * Paginator constructor
     * @param array $records
     * @param int $currentPage
     * @param int $totalPages
     */
    public function __construct(array $records, int $currentPage, int $totalPages)
    {
        $this->records = $records;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
    }

    /**
     * Records getter
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }
}
