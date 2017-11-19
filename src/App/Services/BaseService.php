<?php

namespace App\Services;

use Doctrine\DBAL\Connection;

/**
 * Class BaseService
 * @package App\Services
 */
class BaseService
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * BaseService constructor.
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

}
