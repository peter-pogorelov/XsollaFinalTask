<?php
namespace FinanceApp\Service;

use FinanceApp\Repository\CategoryRepository;

class CategoryService
{
	private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
	
	public function getCategories() 
	{
		return $this->categoryRepository->getCategories();
	}
	
	public function getCategoryByName($name) {
		return $this->categoryRepository->getCategoryByName($name);
	}
}