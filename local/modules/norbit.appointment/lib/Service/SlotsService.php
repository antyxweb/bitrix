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
use Norbit\Appointment\Repository\SlotsRepository;

class SlotsService
{
    private SlotsRepository $slotsRepository;

    /**
     * @param SlotsRepository|null $slotsRepository
     */
    public function __construct(?SlotsRepository $slotsRepository = null)
    {
        $this->slotsRepository = $slotsRepository ?? new SlotsRepository();
    }

    /**
     * Получение специалистов
     *
     * @param array $params
     * @return array|null
     */
    public function getSlots(array $params): ?array
    {
        $select = $params['select'] ?? ['*'];
        $filter = $params['filter'] ?? [];
        $order = $params['order'] ?? [];

        return $this->slotsRepository->getList($select, $filter, $order);
    }

    /**
     * Проверка доступности слота
     *
     * @param array $request
     * @return boolean|null
     */
    public function checkingSlotAvailability($request): bool
    {
        $select = ['ID'];
        $filter = [
            'ID' => $request['slot_id'],
            'ACTIVE' => 'Y',
            'SERVICE_ID' => $request['service_id'],
            'BRANCH_ID' => $request['branch_id'],
            'SPECIALIST_ID' => $request['specialist_id'],
        ];
        $order = [];

        $row = $this->slotsRepository->getList($select, $filter, $order);

        if(count($row)) {
            return true;
        }

        return false;
    }

    /**
     * Установка активности слота
     *
     * @param array $request
     * @return UpdateResult|null
     * @throws NorbitAppointmentException
     */
    public function updateSlotAvailability(array $request, string $active = 'N'): ?UpdateResult
    {
        if (!intval($request['slot_id'])) {
            throw new NorbitAppointmentException('Not ID');
        }
        return $this->slotsRepository->update(intval($request['slot_id']), ['ACTIVE' => $active]);
    }

}
