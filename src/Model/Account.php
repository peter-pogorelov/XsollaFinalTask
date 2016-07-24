<?php
namespace FinanceApp\Model;

class Account
{
    public $id;
	public $user; //id of account owner (User.php)
    public $currency;
    public $balance;
    public $name;

    public function __construct($id, $user, $currency, $balance, $name)
    {
        $this->id = $id;
        $this->user = $user;
        $this->currency = $currency;
		$this->balance = $balance;
        $this->name = $name;
    }
}
