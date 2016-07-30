<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\User;
use FinanceApp\Model\Account;

class AccountRepository extends AbstractRepository
{
	public function getAccounts(User $user){
		$accounts = $this->dbConnection->fetchAll(
            'SELECT id, currency, balance, user, name FROM account WHERE user = ?', 
			[$user->id]
        );
		
		return !is_null($accounts[0]) ? array_map(function($account){
			return new Account($account["id"], $account["user"], $account["currency"], $account["balance"], $account["name"]);
		}, $accounts) : array();
	}
	
	//$params contains filters for accounts
	public function getAccountsByProperty(User $user, $params){
		$cols = $this->getSchema('account');
		
		$query = 'SELECT * FROM account WHERE user = ' . $user->id . ' AND ';
		$types = [];
		
		if(count($cols) !== 0){
			$exist = false;
			foreach($params as $key=>$value){
				if(in_array($key, array_keys($cols))) {
					$exist = true;
					$query .= $key . '=' . AbstractRepository::formatType($cols[$key], $value) . ' AND ';
					
				} else {
					return array();
				}
			}
			
			if($exist){
				$query = chop(trim($query, ' '), 'AND');
				$accounts = $this->dbConnection->fetchAll($query);
				
				return !is_null($accounts[0]) ? array_map(function($account){
					return new Account($account["id"], $account["user"], $account["currency"], $account["balance"], $account["name"]);
				}, $accounts) : array();
			}
		}
		
		return array();
	}
	
	public function getAccountByID(User $user, $id) {
		$accounts = $this->dbConnection->fetchAssoc(
			'SELECT id, currency, balance, user, name FROM account WHERE user = ? AND id = ?', [$user->id, $id]
		);
		
		if(!is_null($accounts['id'])){
			return new Account($accounts['id'], $accounts['user'], $accounts['currency'], 
								$accounts['balance'], $accounts['name']);
		}
		
		return array();
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
		
		//name is not unique so checking is account for this user already exists is important
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
