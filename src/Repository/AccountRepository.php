<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\User;
use FinanceApp\Model\Account;

class AccountRepository extends AbstractRepository
{
	public function getAccounts(User $user){
		$accounts = $this->dbConnection->fetchAll(
            'SELECT id, currency, balance, user, name FROM account WHERE user = ?', [$user->id]
        );
		
		return !is_null($accounts[0]) ? array_map(function($account){
			return new Account($account["id"], $account["user"], $account["currency"], $account["balance"], $account["name"]);
		}, $accounts) : array();
	}
	
	public function getAccountsByProperty(User $user, $property, $value){
		$type = gettype($property);
		
		$accounts = $this->dbConnection->fetchAll(
            'SELECT id, currency, balance, user, name FROM account WHERE user = ? AND ? = ?', 
			[$user->id, $property, $value]
        );
		
		return !is_null($accounts['id']) ? array_map(function($account){
			return new Account($account["id"], $account["user"], $account["currency"], $account["balance"], $account["name"]);
		}, $accounts) : array();
	}
	
	public function getAccountByID(User $user, $id) {
		$accounts = $this->dbConnection->fetchAssoc(
			'SELECT id, currency, balance, user, name FROM account WHERE user = ? AND id = ?', [$user->id, $id]
		);
		
		if(!is_null($accounts['id'])){
			return new Account($accounts['id'], $accounts['user'], $accounts['currency'], 
								$accounts['balance'], $accounts['name']);
		}
		
		return null;
	}
	
	public function deleteAccountByID(User $user, $id) {
		$stmt = $this->dbConnection->executeQuery(
			'DELETE FROM account WHERE user = ? AND id = ?', [$user->id, $id]
		);
		
		return $stmt->rowCount() === 0 ? false : true;
	}
	
	public function createAccount(Account $account){
		$row = $this->dbConnection->fetchAssoc(
			'SELECT * FROM finance.account WHERE user=? AND name=?',
			[$account->user, $account->name]
		);

		if($row['id'] === null) {			
			$stmt = $this->dbConnection->executeQuery(
				'INSERT INTO account (currency, balance, user, name) VALUES (?, ?, ?, ?)', 
				[$account->currency, $account->balance, $account->user, $account->name]
			);
			
			$account->id = $this->dbConnection->lastInsertId();
			
			return $account;
		} else {
			return null;
		}
	}
}
