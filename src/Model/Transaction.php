<?php
namespace FinanceApp\Model;

class Transaction
{
    public $id;
    public $account;
    public $category;
    public $amount;
	public $date;

    public function __construct($id, $account, $category, $amount, $date)
    {
        $this->id = $id;
		$this->account = $account;
		$this->category = $category;
		$this->amount = $amount;
		$this->date = $date;
    }
}
