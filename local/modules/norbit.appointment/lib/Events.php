<?
// пространство имен модуля
namespace Norbit\Appointment;

// пространство имен для получения данных сущности таблицы по событиям
use \Bitrix\Main\Entity\Event;

// класс события
class Events
{
    // для примера выводит поля при каком-либо действии (в регистраторе задано после добавлением)
    static public function eventHandler(Event $event)
    {
        $fields = $event->getParameter("fields");
        echo "<pre>";
        echo "Обработчик события";
        var_dump($fields);
        echo "</pre>";
    }
}
