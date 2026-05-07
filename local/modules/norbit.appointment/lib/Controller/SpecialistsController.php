<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\SpecialistsTable;

class SpecialistsController extends Controller
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

    public function getAction(SpecialistsTable $specialistsTable, int $service_id, int $branch_id)
    {
        $specialistsList = $specialistsTable->getList([
            'select' => ['ID', 'NAME'],
            'filter' => [
                'SERVICE_ID' => $service_id,
                'BRANCH_ID' => $branch_id,
            ],
        ]);

        return $specialistsList->fetchAll();
    }
}