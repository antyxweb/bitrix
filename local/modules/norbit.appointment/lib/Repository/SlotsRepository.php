<?php

namespace Norbit\Appointment\Repository;

use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Norbit\Appointment\ORM\SlotsTable;

class SlotsRepository
{

    /**
     * Добавление категории
     *
     * @param array $data
     * @return AddResult|null
     * @throws \Exception
     */
    public function addSlot(array $data): ?AddResult
    {
        return SlotsTable::add($data);
    }

    /**
     * Обновление категории
     *
     * @param int $id
     * @param array $fields
     * @return UpdateResult|null
     * @throws \Exception
     */
    public function updateSlot(int $id, array $fields): ?UpdateResult
    {
        return SlotsTable::update($id, $fields);
    }

    /**
     * Удаление категории
     *
     * @param int $id
     * @return DeleteResult|null
     * @throws \Exception
     */
    public function deleteSlot(int $id): ?DeleteResult
    {
        return SlotsTable::delete([
            'ID' => $id
        ]);
    }
}