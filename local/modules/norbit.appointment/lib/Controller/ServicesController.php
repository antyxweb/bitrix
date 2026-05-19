<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Norbit\Appointment\Service\ServicesService;

class ServicesController extends Controller
{
    private ServicesService $servicesService;

    /**
     * Конструктор класса AppointmentController.
     * @param ServicesService|null $servicesService     *
     * @param Request|null $request
     */
    public function __construct(?Request $request = null, ?ServicesService $servicesService = null)
    {
        parent::__construct($request);

        $this->servicesService = $servicesService ?? new ServicesService();
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
     * Метод для получения информации об услугах
     *
     * @return array|AjaxJson Возвращает массив с данными услуг или AjaxJson с ошибками.
     */
    public function getAction(): array|AjaxJson
    {
        return $this->servicesService->getServices();
    }
}