<?php
namespace FinanceApp\Service;

use FinanceApp\Model\Transaction;
use FinanceApp\Repository\TransactionRepository;

class TransactionService
{
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }
	
	public function getTransactions($account, $page, $per_page) 
	{
		return $this->transactionRepository->getTransactions($account, $page, $per_page);
	}
	
	public function createTransaction($account, $sum, $category) 
	{
		return $this->transactionRepository->createTransaction($account, $sum, $category);
	}
	
	public function deleteTransaction($account, $id)
	{
		return $this->transactionRepository->deleteTransaction($account, $id);
	}
	
	public function updateTransaction($account, $id, $params) {
		$transaction = new Transaction($id, $account, $params['category'], $params['amount'], $params['date']);
		return $this->transactionRepository->updateTransaction($id, $transaction);
	}
}