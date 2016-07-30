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
		
		//Using atomic operation to execute related queries
		$this->dbConnection->beginTransaction();
		try {
			$this->dbConnection->executeQuery(
				'INSERT INTO transaction (account, category, amount, date) VALUES (?, ?, ?, ?)',
				[$transaction->account, $transaction->category, $transaction->amount, $transaction->date]
			);
			
			$this->dbConnection->executeQuery(
				'UPDATE account SET balance = balance - ? WHERE id = ?',
				[$transaction->amount, $transaction->account]
			);
			
			$this->dbConnection->commit();
			
			$transaction->id = $this->dbConnection->lastInsertId(); //By some reason it doesnt work.
		}
		catch (Exception $e) {
			$this->dbConnection->rollBack();
			return null;
		}
		
		return $transaction;
	}
	
	public function deleteTransaction($account, $id) {
		$transaction = $this->dbConnection->fetchAssoc(
			'SELECT * FROM transaction WHERE account = ? AND id = ?', [$account->id, $id]
		);
		
		if(!is_null($transaction['amount'])) {
			//Using atomic operation to execute related queries
			$this->dbConnection->beginTransaction();
			try{
				$this->dbConnection->executeQuery(
					'DELETE FROM transaction WHERE account = ? AND id = ?', [$account->id, $id]
				);
				
				$this->dbConnection->executeQuery(
					'UPDATE account SET balance = balance + ?',
					[$transaction['amount']]
				);
				
				$this->dbConnection->commit();
			}
			catch(Exception $e) {
				$this->dbConnection->rollBack();
			}
		} else {
			return false;
		}
			
		return true;
	}
	
	public function updateTransaction($id, $transaction) {
		$this->dbConnection->executeQuery(
			'UPDATE transaction SET amount = IFNULL(?, amount), 
			category = IFNULL(?, category), date = IFNULL(?, date) WHERE id=?',
			[$transaction->amount, $transaction->category, $transaction->date, $id]
		);
		
		$updated = $this->dbConnection->fetchAssoc(
			'SELECT * FROM transaction WHERE id=?',
			[$id]
		);
		
		return !is_null($updated['id']) ? new Transaction($updated['id'], $updated['account'], $updated['category'],
			$updated['amount'], $updated['date']) : null;
	}
}
