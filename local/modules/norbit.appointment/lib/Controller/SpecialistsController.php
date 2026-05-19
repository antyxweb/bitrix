<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\Service\SpecialistsService;

class SpecialistsController extends Controller
{
    private SpecialistsService $specialistsService;

    /**
     * Конструктор класса SpecialistsController.
     * @param SpecialistsService|null $specialistsService     *
     * @param Request|null $request
     */
    public function __construct(?Request $request = null, ?SpecialistsService $specialistsService = null)
    {
        parent::__construct($request);

        $this->specialistsService = $specialistsService ?? new SpecialistsService();
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
     * Метод для получения информации о специалистах
     *
     * @param string $service_id ID услуги
     * @param string $branch_id ID филиала
     *
     * @return array|AjaxJson Возвращает массив с данными филиалов или AjaxJson с ошибками.
     */
    public function getAction(int $service_id, int $branch_id): array|AjaxJson
    {
        $params = [
            'filter' => [
                'SERVICE_ID' => $service_id,
                'BRANCH_ID' => $branch_id,
            ]
        ];

        return $this->specialistsService->getSpecialists($params);
    }
}