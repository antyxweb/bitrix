<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error;
use Norbit\Appointment\AppointmentsTable;
use Norbit\Appointment\Services\AppointmentService;
use Norbit\Appointment\SlotsTable;
use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\DB\TransactionException;
use Norbit\Main\Exception\BaseExceptionInterface;

class AppointmentController extends Controller
{
    /**
     * Настройка фильтров для действий
     *
     * @return array
     */
    protected function getDefaultPreFilters()
    {
        return [];
    }

    public function addAction(AppointmentsTable $appointmentsTable, AppointmentService $service, int $service_id, int $branch_id, int $specialist_id, int $slot_id, string $date, string $fill_name, string $phone, Connection $db)
    {
        if(!$service->checkingSlotAvailability($service_id, $branch_id, $specialist_id, $slot_id)) {
            $this->addError(new Error('This slot is not available'));
            return null;
        }

        try {
            $db->startTransaction();

            $result = $appointmentsTable->add(
                array(
                    "ACTIVE" => "Y",
                    "SITE" => "s1",
                    "SERVICE_ID" => $service_id,
                    "BRANCH_ID" => $branch_id,
                    "SPECIALIST_ID" => $specialist_id,
                    "SLOT_ID" => $slot_id,
                    "DATE" => new \Bitrix\Main\Type\DateTime(date($date)),
                    "FULL_NAME" => $fill_name,
                    "PHONE" => $phone,
                )
            );

            if($result->isSuccess()) {
                $db->commitTransaction();

                return [
                    'result' => 'success',
                    'message' => $result->getId(),
                ];
            } else {
                $db->rollbackTransaction();

                return [
                    'result' => 'error',
                    'message' => $result->getErrorMessages(),
                ];
            }
        } catch (Throwable $e) {
            $db->rollbackTransaction();
            $this->addError(new Error('Error add'));
        }
    }

    public function deleteAction(AppointmentsTable $appointmentsTable, SlotsTable $slotsTable, int $id, Connection $db): ?array
    {
        $slotId = $appointmentsTable->getByPrimary($id)->fetchObject()->getSlotId();

        try {
            $db->startTransaction();

            $result = $appointmentsTable->delete($id);

            if($result->isSuccess()) {
                $slotsTable->update($slotId, ['ACTIVE' => 'Y']);

                $db->commitTransaction();

                return [
                    'result' => 'success',
                ];
            } else {
                $db->rollbackTransaction();

                return [
                    'result' => 'error',
                    'message' => $result->getErrorMessages(),
                ];
            }
        } catch (Throwable $e) {
            $db->rollbackTransaction();
            $this->addError(new Error('Error add'));
        }
    }
}