<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\Account;
use FinanceApp\Model\Transaction;

class TransactionRepository extends AbstractRepository
{
	public function getTransactions($account, $page, $per_page){
		$result = $this->dbConnection->fetchAll(
			'SELECT * FROM transaction WHERE account = ? LIMIT ?, ?',
			[intval($account->id), ($page-1) * $per_page, $per_page], 
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT] 
			//this function can not get parameter in LIMIT from context
		);
		
		return !is_null($result[0]) ? array_map(function($trans) {
			return new Transaction($trans['id'], $trans['account'], $trans['category'], $trans['amount'], $trans['date']);
		}, $result) : null;
	}
	
	public function createTransaction($account, $sum, $category) {
		$timestamp = date("Y-m-d H:i:s");
		$transaction = new Transaction(null, $account->id, $category, $sum, $timestamp);
		print($sum);
		$result = $this->dbConnection->executeQuery(
			'INSERT INTO transaction (account, category, amount, date) VALUES (?, ?, ?, ?)',
			[$transaction->account, $transaction->category, $transaction->amount, $transaction->timestamp]
		);
		
		$transaction->id = $this->dbConnection->lastInsertId();
		
		return $transaction;
	}
	
	public function deleteTransaction($account, $id) {
		$stmt = $this->dbConnection->executeQuery(
			'DELETE FROM transaction WHERE account = ? AND id = ?', [$account->id, $id]
		);
		
		return $stmt->rowCount() === 0 ? false : true;
	}
}
