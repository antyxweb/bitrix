<?php

namespace Norbit\Appointment\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Web\Json;
use Norbit\Appointment\SlotsTable;

class AppointmentService
{
    public function checkingSlotAvailability(int $service_id, int $branch_id, int $specialist_id, string $slot_id): bool
    {
        $result = SlotsTable::getList([
            'select' => ['ID'],
            'filter' => [
                'ID' => $slot_id,
                'ACTIVE' => 'Y',
                'SERVICE_ID' => $service_id,
                'BRANCH_ID' => $branch_id,
                'SPECIALIST_ID' => $specialist_id,
            ],
            'limit' => 1,
        ]);
        $row = $result->fetch();

        if(intval($row['ID'])) {
            return true;
        }

        return false;
    }
}
