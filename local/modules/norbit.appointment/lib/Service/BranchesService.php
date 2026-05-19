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
use Norbit\Appointment\Repository\BranchesRepository;

class BranchesService
{
    private BranchesRepository $branchesRepository;

    /**
     * @param BranchesRepository|null $branchesRepository
     */
    public function __construct(?BranchesRepository $branchesRepository = null)
    {
        $this->branchesRepository = $branchesRepository ?? new BranchesRepository();
    }

    /**
     * Получение услуг
     *
     * @param array $params
     * @return array|null
     */
    public function getBranches(array $params): ?array
    {
        $select = $params['select'] ?? ['*'];
        $filter = $params['filter'] ?? [];
        $order = $params['order'] ?? [];

        return $this->branchesRepository->getList($select, $filter, $order);
    }

}
