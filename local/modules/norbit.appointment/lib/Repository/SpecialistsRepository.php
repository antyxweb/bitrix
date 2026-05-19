<?php

namespace Norbit\Appointment\Repository;

use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Norbit\Appointment\ORM\SpecialistsTable;

class SpecialistsRepository
{
    /**
     * Получение специалистов
     *
     * @param array $select Поля выборки
     * @param array $filter Фильтр выборки
     * @param array $order Сортировка
     * @return array|null
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(array $select = ['*'], array $filter = [], array $order = []): ?array
    {
        return SpecialistsTable::query()
            ->setSelect($select)
            ->setFilter($filter)
            ->setOrder($order)
            ->fetchAll();
    }

    /**
     * Добавление специалиста
     *
     * @param array $data
     * @return AddResult|null
     * @throws \Exception
     */
    public function add(array $data): ?AddResult
    {
        return SpecialistsTable::add($data);
    }

    /**
     * Обновление специалиста
     *
     * @param int $id
     * @param array $fields
     * @return UpdateResult|null
     * @throws \Exception
     */
    public function update(int $id, array $fields): ?UpdateResult
    {
        return SpecialistsTable::update($id, $fields);
    }

    /**
     * Удаление специалиста
     *
     * @param int $id
     * @return DeleteResult|null
     * @throws \Exception
     */
    public function delete(int $id): ?DeleteResult
    {
        return SpecialistsTable::delete([
            'ID' => $id
        ]);
    }
}