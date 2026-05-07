<?
// пространство имен модуля
namespace Norbit\Appointment;

// пространство имен для ORM
use \Bitrix\Main\Entity;
// пространство имен для кеша
use \Bitrix\Main\Application;

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

		return array(
			// ID
			new Entity\IntegerField(
				// имя сущности
				"ID",
				array(
					// первичный ключ
					"primary" => true,
					// AUTO INCREMENT
					"autocomplete" => true,
				)
			),
			// Активность
			new Entity\BooleanField(
				'ACTIVE',
				array(
					"values" => array('N', 'Y')
				)
			),
			// Сайт
			new Entity\StringField(
				// имя сущности
				"SITE",
				array(
					// обязательное поле
					"required" => true,
				)
			),
			// Название
			new Entity\StringField(
				// имя сущности
				"NAME",
				array(
					// обязательное поле
					"required" => true,
				)
			),
            // поле для хранения айди автора, информация о которых будет храниться в другой таблице, свяжем данную таблицу с другой
            new Entity\IntegerField(
            // имя сущности
                "SERVICE_ID"
            ),
            // для связи двух таблиц, нужно будет создать поле зависимости, фактически такого поля нет в базе, оно является виртуальным
            new Entity\ReferenceField(
            // имя сущности
                "SERVICE",
                // связываемая сущность другой таблицы
                '\Norbit\Appointment\ServicesTable',
                // this - текущая сущность, ref - связываемая
                array("=this.SERVICE_ID" => "ref.ID")
            ),
		);
	}

	// // события можно задавать прямо в ORM-сущности, для примера запретим изменять поле LINK_PICTURE
	// public static function onBeforeUpdate(Entity\Event $event)
	// {
	// 	$result = new Entity\EventResult;
	// 	$data = $event->getParameter("fields");
	// 	if (isset($data["LINK_PICTURE"])) {
	// 		$result->addError(
	// 			new Entity\FieldError(
	// 				$event->getEntity()->getField("LINK_PICTURE"),
	// 				"Запрещено менять LINK_PICTURE код у баннера"
	// 			)
	// 		);
	// 	}
	// 	return $result;
	// }

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
