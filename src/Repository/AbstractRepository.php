<?php
namespace FinanceApp\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractRepository
{

    protected $dbConnection;

    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

}
