<?
// пространство имен модуля
namespace Norbit\Appointment;

// пространство имен для подключения класса с ORM
use \Norbit\Appointment\ServicesTable;
// пространство имен для получения данных сущности таблицы по событиям
use \Bitrix\Main\Entity\Event;

// основной класс модуля
class NorbitAppointment
{
    // метод для получения строки из таблицы базы данных
    public static function get()
    {
        // запрос к базе
        $result = ServicesTable::getList(
            array(
                'select' => array('*')
            )
        );
        // преобразование запроса от базы
        $row = $result->fetch();
        // распечатываем массив с ответом на экран
        print "<pre>";
        print_r($row);
        print "</pre>";
        // возвращаем ответ от баззы
        return $row;

        return null;
    }
}
