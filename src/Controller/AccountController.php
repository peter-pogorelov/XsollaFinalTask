<?php
namespace FinanceApp\Controller;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends AbstractController
{
	private $accountService;
	
	public function __construct($userService, $accountService) {
		parent::__construct($userService);
		$this->accountService = $accountService;
	}
	
	public function getAccounts(Request $request) {
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		if($request->get('name') !== null) {
			return new JsonResponse($this->accountService->getAccountsByProperty($user, 'name', $request->get('name')));
		}
		else if($request->get('currency') !== null) {
			return new JsonResponse($this->accountService->getAccountsByProperty($user, 'currency', $request->get('currency')));
		}
		
		return new JsonResponse($this->accountService->getAccounts($user));
	}
	
	public function getAccountByID(Request $request, $id){
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		$result = $this->accountService->getAccountByID($user, intval($id));
		
		if($result !== null) {
			return new JsonResponse($result);
		}
		
		return $this->createErrorResponse('This account is not found!');
	}
	
	public function deleteAccountByID(Request $request, $id){
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		if($this->accountService->deleteAccountByID($user, intval($id))) {
			return new JsonResponse(['subject'=>'deleted']);
		}
		
		return $this->createErrorResponse('This account is not found!');
	}
	
	public function createAccount(Request $request) {
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		$name = $request->get('name');
		$currency = $request->get('currency');
		
		if($name !== null && $currency !== null) {
			$acc = $this->accountService->createAccount($user, $name, $currency);
			
			if($acc === null) {
				return $this->createErrorResponse('Entry with this name is already exists!');
			} else {
				return new JsonResponse($acc);
			}
		}
		
		return new $this->createErrorResponse('Name and currency must be specified!');
	}
}
