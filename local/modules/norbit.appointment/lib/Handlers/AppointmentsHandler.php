<?
// пространство имен модуля
namespace Norbit\Appointment\Handlers;

// пространство имен для получения данных сущности таблицы по событиям
use Bitrix\Main\Entity\EventResult;
use \Bitrix\Main\Entity\Event;
use Exception;
use \Norbit\Appointment\Service\SlotsService;

// класс события
class AppointmentsHandler
{
    private SlotsService $slotsService;

    public function __construct(?SlotsService $slotsService = null)
    {
        $this->slotsService = $slotsService ?? new SlotsService();
    }

    /**
     * Метод-обертка для обработчика после добавления записи
     * @param Event $event
     * @return EventResult
     * @throws Exception
     */
    public static function OnAfterAppointmentsAdd(Event $event): EventResult
    {
        $self = new self();
        return $self->runOnAfterAppointmentsAdd($event);
    }

    /**
     * Метод-обертка для обработчика после добавления записи
     * @param Event $event
     * @return EventResult
     * @throws Exception
     */
    private function runOnAfterAppointmentsAdd(Event $event): EventResult
    {
        $result = new EventResult();

        $fields = $event->getParameter("fields");

//        echo "<pre>";
//        var_dump($fields);
//        echo "</pre>";

        if(intval($fields['SLOT_ID'])) {
            $this->slotsService->updateSlotAvailability(['slot_id' => intval($fields['SLOT_ID'])], 'N');
        }

        return $result;
    }
}