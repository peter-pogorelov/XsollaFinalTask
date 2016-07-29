<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\Report;

class ReportRepository extends AbstractRepository
{
	private $valid_params = array('day', 'month', 'year', 'account', 'category');
	private $month_match = array(
		'jan'=>1,
		'feb'=>2,
		'mar'=>3,
		'apr'=>4,
		'may'=>5,
		'jun'=>6,
		'jul'=>7,
		'aug'=>8,
		'sep'=>9,
		'oct'=>10,
		'nov'=>11,
		'dec'=>12
	);
	
	public function validateParams($params){
		if(count(array_diff(array_keys($params), $valid_params)) !== 0){
			return false;
		}
		
		return true;
	}
	
	public function formatNullableObject($param) {
		return (is_null($param) ? 'NULL' : $param);
	}
	
	public function formatNullableString($param) {
		return (is_null($param) ? 'NULL' : '\''.$param.'\'');
	}
	
	public function createReport($user, $params) 
	{
		//if there is some invalid filters
		$keys = array_keys($params);
		if(!$this->validateParams($params)){
			return null;
		}
		
		$first = 'SELECT transaction.amount, transaction.id AS transId, accs.id AS accountId';
		
		$query = ' FROM transaction INNER JOIN
			(SELECT * from account WHERE account.user=' . $user->id. ') AS accs ON accs.id=transaction.account ';
			
		if(in_array('account', $keys)) {
			$query .= 'AND accs.name=' . $params['account']; 
		}
		if(in_array('category', $keys)){
			$first .= ', category.id AS catId';
			$query .= 'INNER JOIN category ON ';
			$query .= 'transaction.category=category.id AND category.name=\'' . $params['category']. '\' ';
		}
		if(in_array('day', $keys)){
			$query .= 'AND DAY(transaction.date)=' . $params['day'] . ' ';
		}
		if(in_array('month', $keys)){
			if(in_array($params['month'], array_keys($this->month_match))){
				$query .= 'AND MONTH(transaction.date)=' . $this->month_match[$params['month']] . ' ';
			} else {
				return null;
			}
		}
		if(in_array('year', $keys)){
			$query .= 'AND YEAR(transaction.date)=' . $params['year'] . ' GROUP BY DATE(transaction.date)';
		}
		
		$query = $first . $query; //this is inner selector.
		
		$queryStartEnd = 'SELECT transaction.amount FROM transaction INNER JOIN (SELECT MIN(p.transId) AS startId, MAX(p.transId) as endId FROM ('. $query .') as p)
		 AS b ON b.startId=transaction.id OR b.endId = transaction.id;';
		$querySumAvg = 'SELECT SUM(p.amount) AS total, AVG(p.amount) AS average FROM ('. $query .') AS p';
		
		try{
			$startEnd = $this->dbConnection->fetchAll($queryStartEnd);
			$sumAvg = $this->dbConnection->fetchAssoc($querySumAvg);
			
			$query = 'INSERT INTO Report (Day, Month, Year, User, Account, Category, Sum, Avg_amount, Start_amount, End_amount) 
			VALUES('.$this->formatNullableObject($params['day'])
			.','.$this->formatNullableObject($params['month'])
			.','.$this->formatNullableObject($params['year'])
			.','.$user->id
			.','.$this->formatNullableString($params['account'])
			.','.$this->formatNullableString($params['category'])
			.','.$sumAvg['total']
			.','.$sumAvg['average']
			.','.$startEnd[0]['amount']
			.','.$startEnd[1]['amount'] .')';
			$this->dbConnection->executeQuery($query);
			
			$result = new Report(
				$this->dbConnection->lastInsertId(),
				$params['day'],
				$params['month'],
				$params['year'],
				$user->id,
				$params['account'],
				$params['category'],
				$sumAvg['total'],
				$sumAvg['average'],
				$startEnd[0]['amount'],
				$startEnd[1]['amount']
			);
			
			return $result;
		} 
		catch(Exception $e){
			return null;
		}
		
		return null;
	}
	
	public function getReport($user, $params) 
	{
		$keys = array_keys($params);
		if(!$this->validateParams($params)){
			return null;
		}
		
		$query = 'SELECT * FROM Report WHERE User=' . $user->id;
		
		if(!is_null($params['account'])){
			$query .= ' AND Account=\'' . $params['account'] . '\'';
		}
		if(!is_null($params['category'])){
			$query .= ' AND Category=\'' . $params['category'] . '\'';
		}
		if(!is_null($params['day'])){
			$query .= ' AND Day=' . $this->formatNullableObject($params['day']);
		}
		if(!is_null($params['month'])){
			$query .= ' AND Month=' . $this->formatNullableObject($params['month']);
		}
		if(!is_null($params['year'])){
			$query .= ' AND Year=' . $this->formatNullableObject($params['year']);
		}
		
		$result = $this->dbConnection->fetchAssoc($query);
		
		return !is_null($result['Id']) ?
            new Report(
				$result['Id'], 
				$result['Day'], 
				$result['Month'], 
				$result['Year'],
				$user->id,
				$result['Account'],
				$result['Category'],
				$result['Sum'],
				$result['Avg_amount'],
				$result['Start_amount'],
				$result['End_amount']
				):null;
	}
}