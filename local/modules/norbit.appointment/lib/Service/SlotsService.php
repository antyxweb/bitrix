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
        $this->slotsRepository = $appointmentRepository ?? new SlotsRepository();
    }

    /**
     * Установка активности слота
     *
     * @param array $request
     * @return UpdateResult|null
     * @throws NorbitAppointmentException
     */
    public function updateSlotAvailability(array $request): ?UpdateResult
    {
        if (!intval($request['slot_id'])) {
            throw new NorbitAppointmentException('Not ID');
        }
        return $this->slotsRepository->updateSlot(intval($request['slot_id']), ['ACTIVE' => 'Y']);
    }

}
