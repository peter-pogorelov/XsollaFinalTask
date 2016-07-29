<?php
namespace FinanceApp\Service;

use FinanceApp\Model\Report;
use FinanceApp\Repository\ReportRepository;

class ReportService
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function createReport($user, $params)
    {
		return $this->reportRepository->createReport($user, $params);
    }

    public function getReport($user, $params)
    {
        return $this->reportRepository->getReport($user, $params);
    }
}