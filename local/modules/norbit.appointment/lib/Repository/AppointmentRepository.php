<?php

namespace Norbit\Appointment\Repository;

use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Norbit\Appointment\ORM\AppointmentsTable;

class AppointmentRepository
{

    /**
     * Добавление записи
     *
     * @param array $data
     * @return AddResult|null
     * @throws \Exception
     */
    public function addAppointment(array $data): ?AddResult
    {
        return AppointmentsTable::add($data);
    }

    /**
     * Обновление записи
     *
     * @param int $id
     * @param array $fields
     * @return UpdateResult|null
     * @throws \Exception
     */
    public function updateAppointment(int $id, array $fields): ?UpdateResult
    {
        return AppointmentsTable::update($id, $fields);
    }

    /**
     * Удаление записи
     *
     * @param int $id
     * @return DeleteResult|null
     * @throws \Exception
     */
    public function deleteAppointment(int $id): ?DeleteResult
    {
        return AppointmentsTable::delete([
            'ID' => $id
        ]);
    }
}