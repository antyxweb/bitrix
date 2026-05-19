<?php
namespace Norbit\Appointment\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\Service\SlotsService;

class SlotsController extends Controller
{
    private SlotsService $slotsService;

    /**
     * Конструктор класса SlotsController.
     * @param SlotsService|null $slotsService     *
     * @param Request|null $request
     */
    public function __construct(?Request $request = null, ?SlotsService $slotsService = null)
    {
        parent::__construct($request);

        $this->slotsService = $slotsService ?? new SlotsService();
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
     * @param string $specialist_id ID специалиста
     *
     * @return array|AjaxJson Возвращает массив с данными слотов или AjaxJson с ошибками.
     */
    public function getAction(int $service_id, int $branch_id, int $specialist_id, string $date): array|AjaxJson
    {
        $params = [
            'filter' => [
                'ACTIVE' => 'Y',
                'SERVICE_ID' => $service_id,
                'BRANCH_ID' => $branch_id,
                'SPECIALIST_ID' => $specialist_id,
                '>=DATE' => new \Bitrix\Main\Type\DateTime($date.' 00:00:00'),
                '<=DATE' => new \Bitrix\Main\Type\DateTime($date.' 23:23:59'),
            ]
        ];

        return $this->slotsService->getSlots($params);
    }
}