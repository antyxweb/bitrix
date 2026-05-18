<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\ORM\SlotsTable;

class SlotsController extends Controller
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

    public function getAction(SlotsTable $slotsTable, int $service_id, int $branch_id, int $specialist_id, string $date)
    {
        $slotsList = $slotsTable->getList([
            'select' => ['ID', 'DATE'],
            'filter' => [
                '=ACTIVE' => 'Y',
                '=SERVICE_ID' => $service_id,
                '=BRANCH_ID' => $branch_id,
                '=SPECIALIST_ID' => $specialist_id,
                '>=DATE' => new \Bitrix\Main\Type\DateTime($date.' 00:00:00'),
                '<=DATE' => new \Bitrix\Main\Type\DateTime($date.' 23:23:59'),
            ],
        ]);

        return $slotsList->fetchAll();
    }
}