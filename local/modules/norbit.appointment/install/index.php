<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для управления (регистрации/удалении) модуля в системе/базе
use Bitrix\Main\ModuleManager;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
// пространство имен с абстрактным классом для любых приложений, любой конкретный класс приложения является наследником этого абстрактного класса
use Bitrix\Main\Application;
use Bitrix\Main\Entity\DataManager;
// пространство имен для работы c ORM
use \Bitrix\Main\Entity\Base;
// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;
// пространство имен для событий
use \Bitrix\Main\EventManager;

// подключаем ORM
use Norbit\Appointment\ORM\ServicesTable;
use Norbit\Appointment\ORM\BranchesTable;
use Norbit\Appointment\ORM\SpecialistsTable;
use Norbit\Appointment\ORM\SlotsTable;
use Norbit\Appointment\ORM\AppointmentsTable;

use Norbit\Appointment\Handlers\AppointmentsHandler;

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
//         // глобальная переменная с обстрактным классом
//         global $APPLICATION;
//         // регистрируем модуль в системе
//         ModuleManager::RegisterModule("norbit.appointment");
//         // создаем таблицы баз данных, необходимые для работы модуля
//         $this->InstallDB();
//         // создаем первую и единственную запись в БД
//         $this->addData();
//         // регистрируем обработчики событий
//         $this->InstallEvents();
//         // копируем файлы, необходимые для работы модуля
//         $this->InstallFiles();
//         // устанавливаем агента
//         //$this->installAgents();
//         // подключаем скрипт с административным прологом и эпилогом
//         $APPLICATION->includeAdminFile(
//             Loc::getMessage('INSTALL_TITLE'),
//             __DIR__ . '/instalInfo.php'
//         );

        // получаем контекст и из него запросы
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // проверяем какой сейчас шаг, если он не существует или меньше 2, то выводим первый шаг установки
        if ($request["step"] < 2) {
            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('INSTALL_TITLE_STEP_1'),
                __DIR__ . '/instalInfo-step1.php'
            );
        }
        // проверяем какой сейчас шаг, усли 2, производим установку
        if ($request["step"] == 2) {
            // регистрируем модуль в системе
            ModuleManager::RegisterModule($this->MODULE_ID);
            // создаем таблицы баз данных, необходимые для работы модуля
            $this->InstallDB();
            // регистрируем обработчики событий
            $this->InstallEvents();
            // копируем файлы, необходимые для работы модуля
            $this->InstallFiles();
            // создаем таблицы баз данных, необходимые для работы модуля
            $this->InstallTables();
            // создаем таблицы баз данных, необходимые для работы модуля
            $this->InstallEvents();
            // заполняем таблицы если дано согласие
            if ($request["add_data"] == "Y") {
                $this->FillingTables();
            }
            // устанавливаем агента
            //$this->installAgents();

            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('INSTALL_TITLE_STEP_2'),
                __DIR__ . '/instalInfo-step2.php'
            );
        }

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод отрабатывает при удалении модуля
    function DoUninstall()
    {
        // получаем контекст и из него запросы
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // проверяем какой сейчас шаг, если он не существует или меньше 2, то выводим первый шаг удаления
        if ($request["step"] < 2) {
            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('DEINSTALL_TITLE_1'),
                __DIR__ . '/deInstalInfo-step1.php'
            );
        }
        // проверяем какой сейчас шаг, усли 2, производим удаление
        if ($request["step"] == 2) {
            // удаляем таблицы баз данных, необходимые для работы модуля
            //$this->UnInstallDB();
            // проверяим ответ формы введеный пользователем на первом шаге
            if ($request["save_data"] == "Y") {
                // удаляем таблицы баз данных, необходимые для работы модуля
                $this->UnInstallDB();
                $this->UnInstallTables();
            }
            // удаляем обработчики событий
            $this->UnInstallEvents();
            // удаляем файлы, необходимые для работы модуля
            $this->UnInstallFiles();
            // удаляем агента
            //$this->unInstallAgents();
            // удаляем регистрацию модуля в системе
            ModuleManager::UnRegisterModule($this->MODULE_ID);

            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('DEINSTALL_TITLE_2'),
                __DIR__ . '/deInstalInfo-step2.php'
            );
        }

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для создания таблицы баз данных
    function InstallDB()
    {
        return true;
    }

    // метод для удаления таблицы баз данных
    function UnInstallDB()
    {
        return true;
    }

    function InstallTables()
    {
        Loader::includeModule($this->MODULE_ID);

        $tables = $this->getTables();
        foreach ($tables as $table) {
            if ($table instanceof DataManager) {
                if (!Application::getConnection()->isTableExists($table::getTableName())) {
                    $table::getEntity()->createDbTable();
                }
            }
        }

        return true;
    }

    function UnInstallTables()
    {
        Loader::includeModule($this->MODULE_ID);

        $tables = $this->getTables();
        foreach ($tables as $table) {
            if ($table instanceof DataManager) {
                if (Application::getConnection()->isTableExists($table::getTableName())) {
                    Application::getConnection()->dropTable($table::getTableName());
                }
            }
        }

        return true;
    }

    function getTables(): array
    {
        return [
            (new \Norbit\Appointment\ORM\ServicesTable),
            (new \Norbit\Appointment\ORM\BranchesTable),
            (new \Norbit\Appointment\ORM\SpecialistsTable),
            (new \Norbit\Appointment\ORM\SlotsTable),
            (new \Norbit\Appointment\ORM\AppointmentsTable),
        ];
    }

    // заполнение таблиц тестовыми данными
    function FillingTables()
    {
        $this->fillingServicesTable();
        $this->fillingBranchesTable();
        $this->fillingSpecialistsTable();
        $this->fillingSlotsTable();
        $this->fillingAppointmentsTable();
    }

    function fillingServicesTable()
    {
        $arServices = [
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "NAME" => "Test Service",
            ],
        ];

        foreach ($arServices as $item) {
            ServicesTable::add($item);
        }
    }

    function fillingBranchesTable()
    {
        $arBranches = [
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "NAME" => "Test Branch",
                "SERVICE_ID" => "1",
            ],
        ];

        foreach ($arBranches as $item) {
            BranchesTable::add($item);
        }
    }

    function fillingSpecialistsTable()
    {
        $arSpecialists = [
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "NAME" => "Test Specialist",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
            ],
        ];

        foreach ($arSpecialists as $item) {
            SpecialistsTable::add($item);
        }
    }

    function fillingSlotsTable()
    {
        $arSlots = [
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00")),
            ],
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+1 hour"))),
            ],
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+2 hour"))),
            ],
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+1 day"))),
            ],
        ];

        foreach ($arSlots as $item) {
            SlotsTable::add($item);
        }
    }

    function fillingAppointmentsTable()
    {
        $arAppointments = [
            [
                "ACTIVE" => "Y",
                "SITE" => "s1",
                "SERVICE_ID" => "1",
                "BRANCH_ID" => "1",
                "SPECIALIST_ID" => "1",
                "SLOT_ID" => "3",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:00:00", strtotime("+2 hour"))),
                "FULL_NAME" => "Pupkin Ivan Petrovish",
                "PHONE" => "+79999999999",
            ],
        ];

        foreach ($arAppointments as $item) {
            AppointmentsTable::add($item);
        }
    }

    // метод для создания обработчика событий
    function InstallEvents()
    {
        // для работы с ORM, есть три типа событий: onBefore<Action> - перед вызовом запроса (можно изменить входные параметры), после следуют валидаторы. on<Action> - уже нельзя изменить входные параметры, после выполняется SQL-запрос. onAfter<Action> - после выполнения операции, операция уже совершена
        // три события <Action> итого 9 событий: Add, Update, Delete
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            // идентификатор модуля, для которого регистрируется событие
            $this->MODULE_ID,
            // тип события, класс называется DataTable, но должно передаваться по имени файла, то есть просто Data
            "\Norbit\Appointment\ORM\Appointments::OnAfterAdd",
            // идентификатор модуля к которому относится регистрируемый обработчик, из какого модуля берется класс, нужно если необходимо связать 2 модуля, если используем один, то дублируем поле с первым
            $this->MODULE_ID,
            // класс обработчика
            AppointmentsHandler::class,
            // метод обработчика
            'OnAfterAppointmentsAdd'
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для удаления обработчика событий
    function UnInstallEvents()
    {
        // удаление событий, аналогично установке
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
        // идентификатор модуля, для которого регистрируется событие
            $this->MODULE_ID,
            // тип события, класс называется DataTable, но должно передаваться по имени файла, то есть просто Data
            "\Norbit\Appointment\ORM\Appointments::OnAfterAdd",
            // идентификатор модуля к которому относится регистрируемый обработчик, из какого модуля берется класс, нужно если необходимо связать 2 модуля, если используем один, то дублируем поле с первым
            $this->MODULE_ID,
            // класс обработчика
            AppointmentsHandler::class,
            // метод обработчика
            'OnAfterAppointmentsAdd'
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
