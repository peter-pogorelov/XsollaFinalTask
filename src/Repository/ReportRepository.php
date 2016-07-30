<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\Report;

class ReportRepository extends AbstractRepository
{
	//all the valid params that can be used for creating report
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
	
	//checking is all the $params are valid
	public function validateParams($params){
		if(count(array_diff(array_keys($params), $this->valid_params)) !== 0){
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
	
	public function nullToZero(&$val){
		$val = is_null($val) ? 0 : $val;
	}
	
	public function createReport($user, $params) 
	{
		//if there is some invalid filters
		$keys = array_keys($params);
		if(!$this->validateParams($params)){
			return null;
		}
		
		//building long query to select all the transactions by $params
		$first = 'SELECT transaction.*';
		
		$query = ' FROM transaction INNER JOIN
			(SELECT * from account WHERE account.user=' . $user->id. ') AS accs ON accs.id=transaction.account ';
			
		if(in_array('account', $keys)) {
			$query .= 'AND accs.name=\'' . $params['account'] . '\' '; 
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
		
		//this query returns first and last transactions in the range (date)
		$queryStartEnd = 'SELECT transaction.amount FROM transaction INNER JOIN (SELECT MIN(p.id) AS startId, MAX(p.id) as endId FROM ('. $query .') as p)
		 AS b ON b.startId=transaction.id OR b.endId = transaction.id;';
		 //this query returns sum of transactions and it's average value
		$querySumAvg = 'SELECT SUM(p.amount) AS total, AVG(p.amount) AS average FROM ('. $query .') AS p';
		
		try{
			$startEnd = $this->dbConnection->fetchAll($queryStartEnd);
			$sumAvg = $this->dbConnection->fetchAssoc($querySumAvg);
			
			$this->nullToZero($sumAvg['total']);
			$this->nullToZero($sumAvg['average']);
			
			$this->nullToZero($startEnd[0]['amount']);
			if(count($startEnd) === 1){ //fixing the problem with 1 observation.
				$startEnd[1] = $startEnd[0];
			} else {
				$this->nullToZero($startEnd[1]['amount']);
			}
			
			//inserting results in Report table
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
				$startEnd[0]['amount'],
				$startEnd[1]['amount'],
				$sumAvg['average']
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
		
		//querying the report if it's already exists.
		$query = 'SELECT * FROM Report WHERE User=' . $user->id;
		
		if(!is_null($params['account'])){
			$query .= ' AND Account=\'' . $params['account'] . '\'';
		} else {
			$query .= ' AND Account is NULL';
		}
		
		if(!is_null($params['category'])){
			$query .= ' AND Category=\'' . $params['category'] . '\'';
		} else {
			$query .= ' AND Category is NULL';
		}
		
		if(!is_null($params['day'])){
			$query .= ' AND Day=' . $this->formatNullableObject($params['day']);
		} else {
			$query .= ' AND Day is NULL';
		}
		
		if(!is_null($params['month'])){
			$query .= ' AND Month=' . $this->formatNullableObject($params['month']);
		} else {
			$query .= ' AND Month is NULL';
		}
		
		if(!is_null($params['year'])){
			$query .= ' AND Year=' . $this->formatNullableObject($params['year']);
		} else {
			$query .= ' AND Year is NULL';
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