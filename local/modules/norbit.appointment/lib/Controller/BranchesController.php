<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\Service\BranchesService;

class BranchesController extends Controller
{
    private BranchesService $branchesService;

    /**
     * Конструктор класса BranchesController.
     * @param BranchesService|null $branchesService     *
     * @param Request|null $request
     */
    public function __construct(?Request $request = null, ?BranchesService $branchesService = null)
    {
        parent::__construct($request);

        $this->branchesService = $branchesService ?? new BranchesService();
    }

    /**
     * Настройка фильтров для действий
     *
     * @return array
     */
    protected function getDefaultPreFilters()
    {
        return [];
    }

    /**
     * Метод для получения информации о филиалах
     *
     * @param string $service_id ID услуги
     *
     * @return array|AjaxJson Возвращает массив с данными филиалов или AjaxJson с ошибками.
     */
    public function getAction(int $service_id): array|AjaxJson
    {
        $params = [
            'filter' => [
                'SERVICE_ID' => $service_id
            ]
        ];
        return $this->branchesService->getBranches($params);
    }
}