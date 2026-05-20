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

// сущность ORM унаследованная от DataManager
class BranchesTable extends Entity\DataManager
{
	// название таблицы в базе данных, если не указывать данную функцию, то таблица в бд сформируется автоматически из неймспейса
	public static function getTableName()
	{
		return "n_appointment_branches";
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
            (new Entity\StringField('NAME'))
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_NAME')
                ),
            (new Entity\IntegerField('SERVICE_ID'))
                ->setParameter('IS_REFERENCE_ID', true)
                ->setParameter('REFERENCE_CLASS', ServicesTable::class)
                ->configureRequired()
                ->configureTitle(
                    Loc::getMessage('APPOINTMENTS_SERVICE_ID')
                ),
        ];
	}

	// очистка тегированного кеша при добавлении
	public static function onAfterAdd(Entity\Event $event)
	{
        BranchesTable::clearCache();
	}
	// очистка тегированного кеша при изменении
	public static function onAfterUpdate(Entity\Event $event)
	{
        BranchesTable::clearCache();
	}
	// очистка тегированного кеша при удалении
	public static function onAfterDelete(Entity\Event $event)
	{
        BranchesTable::clearCache();
	}
	// основной метод очистки кеша по тегу
	public static function clearCache()
	{
		// служба пометки кеша тегами
		$taggedCache = Application::getInstance()->getTaggedCache();
		$taggedCache->clearByTag('appointment');
	}
}
