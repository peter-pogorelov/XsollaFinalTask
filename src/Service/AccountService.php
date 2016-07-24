<?php
namespace FinanceApp\Service;

use FinanceApp\Repository\AccountRepository;
use FinanceApp\Model\Account;
use FinanceApp\Model\User;

class AccountService
{
	private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }
	
	public function getAccounts(User $user) 
	{
		return $this->accountRepository->getAccounts($user);
	}
	
	public function getAccountsByProperty(User $user, $property, $value)
	{
		return $this->accountRepository->getAccountsByProperty($user, $property, $value);
	}
	
	public function getAccountByID(User $user, $id) 
	{
		return $this->accountRepository->getAccountByID($user, $id);
	}
	
	public function deleteAccountByID(User $user, $id) 
	{
		return $this->accountRepository->deleteAccountByID($user, $id);
	}
	
	public function createAccount(User $user, $name, $currency) {
		$account = new Account( null,
								$user->id,
								$currency,
								0,
								$name);
		
		return $this->accountRepository->createAccount($account);
	}
}