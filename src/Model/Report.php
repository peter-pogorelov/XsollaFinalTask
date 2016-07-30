<?php
namespace FinanceApp\Model;

class Report
{
	public $id;
	
    public $day;
	public $month;
	public $year;
	
    public $user;
    public $account;
    public $category;
	
	public $sum;
	public $start_amount;
	public $end_amount;
	public $avg_amount;

    public function __construct(
		$id, $day, $month, $year, $user, $account, $category,
		$sum, $start_amount, $end_amount, $avg_amount
	)
    {
		$this->id = $id;
		
        $this->day = $day;
		$this->month = $month;
		$this->year = $year;
		
		$this->user = $user;
		$this->account = $account;
		$this->category = $category;
		
		$this->sum = $sum;
		$this->start_amount = $start_amount;
		$this->end_amount = $end_amount;
		$this->avg_amount = $avg_amount;
    }
}
