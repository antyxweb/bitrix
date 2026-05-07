<?
// пространство имен модуля
namespace Norbit\Appointment;

// пространство имен для ORM
use \Bitrix\Main\Entity;
// пространство имен для кеша
use \Bitrix\Main\Application;

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
					"values" => array('N', 'Y'),
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
            // поле для хранения айди услуги, информация о которых будет храниться в другой таблице, свяжем данную таблицу с другой
            new Entity\IntegerField(
            // имя сущности
                "SERVICE_ID",
                array(
                    "NOT_SHOWED" => true,
                )
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
            // поле для хранения айди филиала, информация о которых будет храниться в другой таблице, свяжем данную таблицу с другой
            new Entity\IntegerField(
            // имя сущности
                "BRANCH_ID",
                array(
                    "NOT_SHOWED" => true,
                )
            ),
            // для связи двух таблиц, нужно будет создать поле зависимости, фактически такого поля нет в базе, оно является виртуальным
            new Entity\ReferenceField(
            // имя сущности
                "BRANCH",
                // связываемая сущность другой таблицы
                '\Norbit\Appointment\BranchesTable',
                // this - текущая сущность, ref - связываемая
                array("=this.BRANCH_ID" => "ref.ID")
            ),
            // поле для хранения айди специалиста, информация о которых будет храниться в другой таблице, свяжем данную таблицу с другой
            new Entity\IntegerField(
            // имя сущности
                "SPECIALIST_ID",
                array(
                    "NOT_SHOWED" => true,
                )
            ),
            // для связи двух таблиц, нужно будет создать поле зависимости, фактически такого поля нет в базе, оно является виртуальным
            new Entity\ReferenceField(
            // имя сущности
                "SPECIALIST",
                // связываемая сущность другой таблицы
                '\Norbit\Appointment\SpecialistsTable',
                // this - текущая сущность, ref - связываемая
                array("=this.SPECIALIST_ID" => "ref.ID")
            ),
            // поле для хранения айди специалиста, информация о которых будет храниться в другой таблице, свяжем данную таблицу с другой
            new Entity\IntegerField(
            // имя сущности
                "SLOT_ID",
                array(
                    "NOT_SHOWED" => true,
                )
            ),
            // для связи двух таблиц, нужно будет создать поле зависимости, фактически такого поля нет в базе, оно является виртуальным
            new Entity\ReferenceField(
            // имя сущности
                "SLOT",
                // связываемая сущность другой таблицы
                '\Norbit\Appointment\SlotsTable',
                // this - текущая сущность, ref - связываемая
                array("=this.SLOT_ID" => "ref.ID")
            ),
            // дата и время заполнения
            new Entity\DatetimeField(
            // имя сущности
                "DATE",
                array(
                    'required' => true,
                )
            ),
            // ФИО
            new Entity\StringField(
            // имя сущности
                "FULL_NAME",
                array(
                    // обязательное поле
                    "required" => true,
                )
            ),
            // Телефон
            new Entity\StringField(
            // имя сущности
                "PHONE",
                array(
                    // обязательное поле
                    "required" => true,
                )
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
        $fields = $event->getParameter("fields");
        if($slotId = $fields['SLOT_ID']) {
            \Norbit\Appointment\SlotsTable::update($slotId, ['ACTIVE' => 'N']);
        }

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
