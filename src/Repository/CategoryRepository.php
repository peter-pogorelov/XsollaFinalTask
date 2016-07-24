<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\Category;

class CategoryRepository extends AbstractRepository
{
	public function getCategories() 
	{
		$categoryRows = $this->dbConnection->fetchAll(
            'SELECT * FROM category'
        );
		
		return !is_null($categoryRows) ? array_map(function($category){
			return new Category($category['id'], $category['name']);
		}, $categoryRows) : null;
	}
	
	public function getCategoryByName($name) {
		$categoryRows = $this->dbConnection->fetchAssoc(
            'SELECT * FROM category WHERE name = ?', [$name]
        );
		return !is_null($categoryRows['id']) ? new Category($categoryRows['id'], $categoryRows['name']) : null;
	}
}
