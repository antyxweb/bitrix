<?php

namespace Norbit\Appointment\Service;

use Bitrix\Main\Error;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Web\Json;
use Norbit\Appointment\Exception\NorbitAppointmentException;
use Norbit\Appointment\Repository\ServicesRepository;

class ServicesService
{
    private ServicesRepository $servicesRepository;

    /**
     * @param ServicesRepository|null $servicesRepository
     */
    public function __construct(?ServicesRepository $servicesRepository = null)
    {
        $this->servicesRepository = $servicesRepository ?? new ServicesRepository();
    }

    /**
     * Получение услуг
     *
     * @param array $params
     * @return array|null
     */
    public function getServices(): ?array
    {
        return $this->servicesRepository->getList();
    }

}
