<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\ORM\ServicesTable;

class ServicesController extends Controller
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

    public function getAction(ServicesTable $servicesTable)
    {
        $servicesList = $servicesTable->getList();

        return $servicesList->fetchAll();
    }
}