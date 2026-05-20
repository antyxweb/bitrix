<?
// пространство имен модуля
namespace Norbit\Appointment\ORM;

// пространство имен для ORM
use \Bitrix\Main\Entity;
// пространство имен для кеша
use \Bitrix\Main\Application;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Localization\Loc;
use Norbit\Appointment\ORM\ServicesTable;
use Norbit\Appointment\ORM\BranchesTable;
use Norbit\Appointment\ORM\SpecialistsTable;
use Norbit\Appointment\ORM\SlotsTable;

// сущность ORM унаследованная от DataManager
class AppointmentsTable extends Entity\DataManager
{
	// название таблицы в базе данных, если не указывать данную функцию, то таблица в бд сформируется автоматически из неймспейса
	public static function getTableName()
	{
		return "n_appointment_appointments";
	}

	// подключение к БД, если не указывать, то будет использовано значение по умолчанию подключения из файла .settings.php. Если указать, то можно выбрать подключение, которое может быть описано в .setting.php
	public static function getConnectionName()
	{
		return "default";
	}

	// метод возвращающий структуру ORM-сущности
	public static function getMap()
	{
		/*
         * Типы полей: 
         * DatetimeField - дата и время
         * DateField - дата
         * BooleanField - логическое поле да/нет
         * IntegerField - числовой формат
         * FloatField - числовой дробный формат
         * EnumField - список, можно передавать только заданные значения
         * TextField - text
         * StringField - varchar
         */

        return [
            (new Entity\IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_ID')
                ),
            (new Entity\BooleanField('ACTIVE'))
                ->configureValues('N', 'Y')
                ->configureDefaultValue('Y')
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_ACTIVE')
                ),
            (new Entity\StringField('SITE'))
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_SITE')
                ),
            (new Entity\IntegerField('SERVICE_ID'))
                ->setParameter('IS_REFERENCE_ID', true)
                ->setParameter('REFERENCE_CLASS', ServicesTable::class)
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_SERVICE_ID')
                ),
            (new Entity\IntegerField('BRANCH_ID'))
                ->setParameter('IS_REFERENCE_ID', true)
                ->setParameter('REFERENCE_CLASS', BranchesTable::class)
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_BRANCH_ID')
                ),
            (new Entity\IntegerField('SPECIALIST_ID'))
                ->setParameter('IS_REFERENCE_ID', true)
                ->setParameter('REFERENCE_CLASS', SpecialistsTable::class)
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_SPECIALIST_ID')
                ),
            (new Entity\IntegerField('SLOT_ID'))
                ->setParameter('IS_REFERENCE_ID', true)
                ->setParameter('REFERENCE_CLASS', SlotsTable::class)
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_SLOT_ID')
                ),
            (new Entity\StringField('FULL_NAME'))
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_FULL_NAME')
                ),
            (new Entity\StringField('PHONE'))
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_PHONE')
                ),
        ];
	}

	// очистка тегированного кеша при добавлении
	public static function onAfterAdd(Entity\Event $event)
	{
        AppointmentsTable::clearCache();
	}
	// очистка тегированного кеша при изменении
	public static function onAfterUpdate(Entity\Event $event)
	{
        AppointmentsTable::clearCache();
	}
	// очистка тегированного кеша при удалении
	public static function onAfterDelete(Entity\Event $event)
	{
        AppointmentsTable::clearCache();
	}
	// основной метод очистки кеша по тегу
	public static function clearCache()
	{
		// служба пометки кеша тегами
		$taggedCache = Application::getInstance()->getTaggedCache();
		$taggedCache->clearByTag('appointment');
	}
}
