<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для управления (регистрации/удалении) модуля в системе/базе
use Bitrix\Main\ModuleManager;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
// пространство имен с абстрактным классом для любых приложений, любой конкретный класс приложения является наследником этого абстрактного класса
use Bitrix\Main\Application;
// пространство имен для работы c ORM
use \Bitrix\Main\Entity\Base;
// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;
// пространство имен для событий
use \Bitrix\Main\EventManager;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

class Norbit_Appointment extends CModule
{
    // переменные модуля
    public  $MODULE_ID;
    public  $MODULE_VERSION;
    public  $MODULE_VERSION_DATE;
    public  $MODULE_NAME;
    public  $MODULE_DESCRIPTION;
    public  $PARTNER_NAME;
    public  $PARTNER_URI;
    public  $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public  $MODULE_GROUP_RIGHTS;
    public  $errors;

    // конструктор класса, вызывается автоматически при обращение к классу
    function __construct()
    {
        // создаем пустой массив для файла version.php
        $arModuleVersion = array();
        // подключаем файл version.php
        include_once(__DIR__ . '/version.php');

        // версия модуля
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        // дата релиза версии модуля
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        // id модуля
        $this->MODULE_ID = "norbit.appointment";
        // название модуля
        $this->MODULE_NAME = "SPA-виджет";
        // описание модуля
        $this->MODULE_DESCRIPTION = "Модуль для записи на услуги с многошаговой формой";
        // имя партнера выпустившего модуль
        $this->PARTNER_NAME = "Норбит";
        // ссылка на рисурс партнера выпустившего модуль
        $this->PARTNER_URI = "https://www.norbit.ru";
        // если указано, то на странице прав доступа будут показаны администраторы и группы
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        // если указано, то на странице редактирования групп будет отображаться этот модуль
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    // метод отрабатывает при установке модуля
    function DoInstall()
    {
         // глобальная переменная с обстрактным классом
         global $APPLICATION;
         // регистрируем модуль в системе
         ModuleManager::RegisterModule("norbit.appointment");
         // создаем таблицы баз данных, необходимые для работы модуля
         $this->InstallDB();
         // создаем первую и единственную запись в БД
         $this->addData();
         // регистрируем обработчики событий
         $this->InstallEvents();
         // копируем файлы, необходимые для работы модуля
         $this->InstallFiles();
         // устанавливаем агента
         //$this->installAgents();
         // подключаем скрипт с административным прологом и эпилогом
         $APPLICATION->includeAdminFile(
             Loc::getMessage('INSTALL_TITLE'),
             __DIR__ . '/instalInfo.php'
         );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод отрабатывает при удалении модуля
    function DoUninstall()
    {
         // глобальная переменная с обстрактным классом
         global $APPLICATION;
         // удаляем таблицы баз данных, необходимые для работы модуля
         $this->UnInstallDB();
         // удаляем обработчики событий
         $this->UnInstallEvents();
         // удаляем файлы, необходимые для работы модуля
         $this->UnInstallFiles();
         // удаляем агента
         //$this->unInstallAgents();
         // удаляем регистрацию модуля в системе
         ModuleManager::UnRegisterModule("norbit.appointment");
         // подключаем скрипт с административным прологом и эпилогом
         $APPLICATION->includeAdminFile(
             Loc::getMessage('DEINSTALL_TITLE'),
             __DIR__ . '/deInstalInfo.php'
         );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для создания таблицы баз данных
    function InstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Norbit\Appointment\ServicesTable::getConnectionName())->isTableExists(Base::getInstance("\Norbit\Appointment\ServicesTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\Norbit\Appointment\ServicesTable")->createDbTable();
        }
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Norbit\Appointment\BranchesTable::getConnectionName())->isTableExists(Base::getInstance("\Norbit\Appointment\BranchesTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\Norbit\Appointment\BranchesTable")->createDbTable();
        }
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Norbit\Appointment\SpecialistsTable::getConnectionName())->isTableExists(Base::getInstance("\Norbit\Appointment\SpecialistsTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\Norbit\Appointment\SpecialistsTable")->createDbTable();
        }
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Norbit\Appointment\SlotsTable::getConnectionName())->isTableExists(Base::getInstance("\Norbit\Appointment\SlotsTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\Norbit\Appointment\SlotsTable")->createDbTable();
        }
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Norbit\Appointment\AppointmentsTable::getConnectionName())->isTableExists(Base::getInstance("\Norbit\Appointment\AppointmentsTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\Norbit\Appointment\AppointmentsTable")->createDbTable();
        }
    }

    // метод для удаления таблицы баз данных
    function UnInstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\Norbit\Appointment\ServicesTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Norbit\Appointment\ServicesTable")->getDBTableName());
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\Norbit\Appointment\BranchesTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Norbit\Appointment\BranchesTable")->getDBTableName());
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\Norbit\Appointment\SpecialistsTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Norbit\Appointment\SpecialistsTable")->getDBTableName());
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\Norbit\Appointment\SlotsTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Norbit\Appointment\SlotsTable")->getDBTableName());
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\Norbit\Appointment\AppointmentsTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Norbit\Appointment\AppointmentsTable")->getDBTableName());

        // удаляем параметры модуля из базы данных битрикс
        Option::delete($this->MODULE_ID);
    }

    // метод для создания обработчика событий
    function InstallEvents()
    {
        // для работы с ORM, есть три типа событий: onBefore<Action> - перед вызовом запроса (можно изменить входные параметры), после следуют валидаторы. on<Action> - уже нельзя изменить входные параметры, после выполняется SQL-запрос. onAfter<Action> - после выполнения операции, операция уже совершена
        // три события <Action> итого 9 событий: Add, Update, Delete
        EventManager::getInstance()->registerEventHandler(
        // идентификатор модуля, для которого регистрируется событие
            $this->MODULE_ID,
            // тип события, класс называется DataTable, но должно передаваться по имени файла, то есть просто Data
            "\Norbit\Appointment\Appointments::OnAfterAdd",
            // идентификатор модуля к которому относится регистрируемый обработчик, из какого модуля берется класс, нужно если необходимо связать 2 модуля, если используем один, то дублируем поле с первым
            $this->MODULE_ID,
            // класс обработчика
            "\Norbit\Appointment\Events",
            // метод обработчика
            'eventHandler'
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для удаления обработчика событий
    function UnInstallEvents()
    {
        // удаление событий, аналогично установке
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "\Norbit\Appointment\Appointments::OnAfterAdd",
            $this->MODULE_ID,
            "\Norbit\Appointment\Events",
            'eventHandler'
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для копирования файлов модуля при установке
    function InstallFiles()
    {
        // скопируем файлы на страницы админки из папки в битрикс, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );

        // копируем файлы страниц, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . '/routes',
            $_SERVER["DOCUMENT_ROOT"] . '/local/routes',
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для удаления файлов модуля при удалении
    function UnInstallFiles()
    {
        // удалим файлы из папки в битрикс на страницы админки, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );

        // удалим файлы страниц, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/routes",
            $_SERVER["DOCUMENT_ROOT"] . "/local/routes"
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // заполнение таблиц тестовыми данными
    function addData()
    {
        // подключаем модуль для видимости ORM класса
        Loader::includeModule($this->MODULE_ID);

        // добавляем запись в таблицу БД
        \Norbit\Appointment\ServicesTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "NAME" => "Test Service",
            )
        );

        // добавляем запись в таблицу БД
        \Norbit\Appointment\BranchesTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "NAME" => "Test Branch",
                "SERVICE_ID" => "1",
            )
        );

        // добавляем запись в таблицу БД
        \Norbit\Appointment\SpecialistsTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "NAME" => "Test Specialist",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
            )
        );

        // добавляем запись в таблицу БД
        \Norbit\Appointment\SlotsTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00")),
            ),
        );
        \Norbit\Appointment\SlotsTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+1 hour"))),
            ),
        );
        \Norbit\Appointment\SlotsTable::add(
            array(
                "ACTIVE" => "N",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+2 hour"))),
            ),
        );
        \Norbit\Appointment\SlotsTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+1 day"))),
            ),
        );

        // добавляем запись в таблицу БД
        \Norbit\Appointment\AppointmentsTable::add(
            array(
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "SLOT_ID" => "3",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+2 hour"))),
                "FULL_NAME" => "Pupkin Ivan Petrovish",
                "PHONE" => "+79999999999",
            )
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // установка агентов
    function installAgents()
    {
        //
    }

    // удаление агентов
    function unInstallAgents()
    {
        //
    }
}
