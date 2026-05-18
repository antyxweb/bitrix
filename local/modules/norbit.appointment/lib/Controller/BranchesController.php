<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\ORM\BranchesTable;

class BranchesController extends Controller
{
    /**
     * Настройка фильтров для действий
     *
     * @return array
     */
    protected function getDefaultPreFilters()
    {
        return [];
    }

    public function getAction(BranchesTable $branchesTable, int $service_id)
    {
        $branchesList = $branchesTable->getList([
            'select' => ['ID', 'NAME'],
            'filter' => [
                'SERVICE_ID' => $service_id,
            ],
        ]);

        return $branchesList->fetchAll();
    }
}