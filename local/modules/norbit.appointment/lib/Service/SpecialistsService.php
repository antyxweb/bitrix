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
use Norbit\Appointment\Repository\SpecialistsRepository;

class SpecialistsService
{
    private SpecialistsRepository $specialistsRepository;

    /**
     * @param SpecialistsRepository|null $specialistsRepository
     */
    public function __construct(?SpecialistsRepository $specialistsRepository = null)
    {
        $this->specialistsRepository = $specialistsRepository ?? new SpecialistsRepository();
    }

    /**
     * Получение специалистов
     *
     * @param array $params
     * @return array|null
     */
    public function getSpecialists(array $params): ?array
    {
        $select = $params['select'] ?? ['*'];
        $filter = $params['filter'] ?? [];
        $order = $params['order'] ?? [];

        return $this->specialistsRepository->getList($select, $filter, $order);
    }

}
