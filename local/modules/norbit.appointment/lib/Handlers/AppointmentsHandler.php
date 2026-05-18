<?
// пространство имен модуля
namespace Norbit\Appointment\Handlers;

// пространство имен для получения данных сущности таблицы по событиям
use \Bitrix\Main\Entity\Event;

// класс события
class AppointmentsHandler
{
    // для примера выводит поля при каком-либо действии (в регистраторе задано после добавлением)
    static public function OnAfterAppointmentsAdd(Event $event)
    {
        $fields = $event->getParameter("fields");

//        echo "<pre>";
//        var_dump($fields);
//        echo "</pre>";
    }
}