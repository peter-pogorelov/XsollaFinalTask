<?php
namespace FinanceApp\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractRepository
{
    protected $dbConnection;
	private $dbName;

    public function __construct(Connection $dbConnection, $dbName)
    {
        $this->dbConnection = $dbConnection;
		$this->dbName = $dbName;
    }
	
	//Get names of columns of current table
	public function getSchema($table){
		$res = $this->dbConnection->fetchAll(
			"SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?",
			[$table, $this->dbName],
			[\PDO::PARAM_STR, \PDO::PARAM_STR]
		);

		$assoc = [];
		
		for($i = 0; $i < count($res); $i++) {
			$assoc[$res[$i]['COLUMN_NAME']] = $res[$i]['DATA_TYPE'];
		}
	
		return $assoc;
	}
	
	//format type according to SQL syntax
	public static function formatType($type, $param) {
		switch($type) {
			case 'string': case 'varchar':
				return "'" . $param . "'";
			default:
				return $param;
		}
	}
}
