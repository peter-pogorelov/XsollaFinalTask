<?php
namespace FinanceApp\Controller;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends AbstractController
{
	private $accountService;
	private $transactionService;
	private $categoryService;
	
	public function __construct($userService, $accountService, $transactionService, $categoryService) {
		parent::__construct($userService);
		
		$this->accountService = $accountService;
		$this->transactionService = $transactionService;
		$this->categoryService = $categoryService;
	}
	
	public function getTransactions(Request $request, $id) {
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		$page = intval($request->query->get('page'));
		$per_page = intval($request->query->get('per_page'));
		$valid_values = array(10, 50, 100, 150, 200);
		
		
		$account = $this->accountService->getAccountByID($user, intval($id));
		
		if($account !== null){
			if($per_page != 0){
				if(!in_array($per_page, $valid_values)) {
					$this->createErrorResponse('Valid values for per_page: 10, 50, 100, 150, 200!');
				}
				
				if(page < 1) {
					$this->createErrorResponse('Negative or zero page number is not allowed!');
				}
			} else {
				$per_page = 100;
				$page = 1;
			}
			return new JsonResponse($this->transactionService->getTransactions($account, $page, $per_page));
		} else {
			return $this->createErrorResponse('Account id is invalid!');
		}
		
		return $this->createErrorResponse('Something went wrong!');
	}
	
	public function createTransaction(Request $request, $id) {
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		$account = $this->accountService->getAccountByID($user, intval($id));
		$sum = $request->get('sum');
		$category = $request->get('category');
		
		if($sum !== null && $category !== null) {
			if($account !== null) {
				if(($category = $this->categoryService->getCategoryByName($category)) !== null) {
					return new JsonResponse($this->transactionService->createTransaction($account, $sum, $category->name));
				} else {
					return $this->createErrorResponse('Category is not registered!');
				}
			} else {
				return $this->createErrorResponse('Account id is invalid!');
			}
		} else {
			return $this->createErrorResponse('Sum or category is not specified!');
		}
		
		return $this->createErrorResponse('Something went wrong!');
	}
	
	public function deleteTransaction(Request $request, $id, $trans) {
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		$account = $this->accountService->getAccountByID($user, intval($id));
		
		if($account !== null) {
			if($this->transactionService->deleteTransaction($account, intval($trans))){
				return new JsonResponse(['subject'=>'deleted']);
			} else {
				return $this->createErrorResponse('Record is not found!');
			}
		} else {
			return $this->createErrorResponse('Account id is invalid!');
		}
	}
}
