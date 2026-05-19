<?php

namespace Norbit\Appointment\Repository;

use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Norbit\Appointment\ORM\BranchesTable;

class BranchesRepository
{
    /**
     * Получение филиалов
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
        return BranchesTable::query()
            ->setSelect($select)
            ->setFilter($filter)
            ->setOrder($order)
            ->fetchAll();
    }

    /**
     * Добавление филиала
     *
     * @param array $data
     * @return AddResult|null
     * @throws \Exception
     */
    public function add(array $data): ?AddResult
    {
        return BranchesTable::add($data);
    }

    /**
     * Обновление филиала
     *
     * @param int $id
     * @param array $fields
     * @return UpdateResult|null
     * @throws \Exception
     */
    public function update(int $id, array $fields): ?UpdateResult
    {
        return BranchesTable::update($id, $fields);
    }

    /**
     * Удаление филиала
     *
     * @param int $id
     * @return DeleteResult|null
     * @throws \Exception
     */
    public function delete(int $id): ?DeleteResult
    {
        return BranchesTable::delete([
            'ID' => $id
        ]);
    }
}