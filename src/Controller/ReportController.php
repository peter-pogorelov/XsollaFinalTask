<?php
namespace FinanceApp\Controller;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends AbstractController
{
	private $accountService;
	private $reportService;
	private $categoryService;
	
	public function __construct($userService, $accountService, $reportService, $categoryService) {
		parent::__construct($userService);
		
		$this->accountService = $accountService;
		$this->reportService = $reportService;
		$this->categoryService = $categoryService;
	}
	
	public function getReport(Request $request)
	{
		$user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }
		
		$params = $request->query->all(); //year, month, day, category(str), account(str)
		$report = $this->reportService->getReport($user, $params); //TODO: updated transactions is not recorded.
		if(is_null($report)) {
			$report = $this->reportService->createReport($user, $params);
		}
		
		return new JsonResponse($report);
	}
}
