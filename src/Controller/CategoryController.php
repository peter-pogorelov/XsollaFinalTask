<?php
namespace FinanceApp\Controller;

use FinanceApp\Service\CategoryService;
use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//It is not important to be registered to see categories
class CategoryController
{
	private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
	
	public function getCategories(Request $request)
	{
		return new JsonResponse($this->categoryService->getCategories());
	}
}
