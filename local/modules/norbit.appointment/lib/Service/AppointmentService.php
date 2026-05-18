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
use Norbit\Appointment\ORM\SlotsTable;
use Norbit\Appointment\Exception\NorbitAppointmentException;
use Norbit\Appointment\Repository\AppointmentRepository;

class AppointmentService
{
    private AppointmentRepository $appointmentRepository;

    /**
     * @param AppointmentRepository|null $appointmentRepository
     */
    public function __construct(?AppointmentRepository $appointmentRepository = null)
    {
        $this->appointmentRepository = $appointmentRepository ?? new AppointmentRepository();
    }

    public function checkingSlotAvailability($request): bool
    {
        $result = SlotsTable::getList([
            'select' => ['ID'],
            'filter' => [
                'ID' => $request['slot_id'],
                'ACTIVE' => 'Y',
                'SERVICE_ID' => $request['service_id'],
                'BRANCH_ID' => $request['branch_id'],
                'SPECIALIST_ID' => $request['specialist_id'],
            ],
            'limit' => 1,
        ]);
        $row = $result->fetch();

        if(intval($row['ID'])) {
            return true;
        }

        return false;
    }

    public function addAppointment(array $request): ?AddResult
    {
        $fields = $this->getAppointmentFields($request);
        return $this->appointmentRepository->addAppointment($fields);
    }

    /**
     * Удаление категории
     *
     * @param array $request
     * @return DeleteResult|null
     * @throws SbktsUniversalException
     */
    public function deleteAppointment(array $request): ?DeleteResult
    {
        if (!intval($request['id'])) {
            throw new NorbitAppointmentException('Not ID');
        }
        return $this->appointmentRepository->deleteAppointment(intval($request['id']));
    }

    /**
     * Получение полей
     *
     * @param array $request
     * @return array
     * @throws SbktsUniversalException
     */
    private function getAppointmentFields(array $request): array
    {
        $fields = [
            "ACTIVE" => "Y",
            "SITE" => "s1",
            "SERVICE_ID" => $request['service_id'],
            "BRANCH_ID" => $request['branch_id'],
            "SPECIALIST_ID" => $request['specialist_id'],
            "SLOT_ID" => $request['slot_id'],
            "DATE" => new \Bitrix\Main\Type\DateTime(date($request['date'])),
            "FULL_NAME" => $request['full_name'],
            "PHONE" => $request['phone'],
        ];

        return $fields;
    }
}
