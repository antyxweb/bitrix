<?php
namespace Norbit\Appointment\Controller;

use \Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Norbit\Appointment\ORM\AppointmentsTable;
use Norbit\Appointment\Service\AppointmentService;
use Norbit\Appointment\Service\SlotsService;
use Norbit\Appointment\ORM\SlotsTable;
use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\DB\TransactionException;
use Norbit\Main\Exception\BaseExceptionInterface;

class AppointmentController extends Controller
{
    private AppointmentsTable $appointmentsTable;
    private AppointmentService $appointmentService;
    private SlotsService $slotsService;

    /**
     * Конструктор класса AppointmentController.
     * @param AppointmentsTable|null $appointmentsTable     *
     * @param AppointmentService|null $appointmentService     *
     * @param SlotsService|null $slotsService     *
     * @param Request|null $request
     */
    public function __construct(?Request $request = null, ?AppointmentsTable $appointmentsTable = null, ?AppointmentService $appointmentService = null, ?SlotsService $slotsService = null)
    {
        parent::__construct($request);

        $this->appointmentsTable = $appointmentsTable ?? new AppointmentsTable();
        $this->appointmentService = $appointmentService ?? new AppointmentService();
        $this->slotsService = $slotsService ?? new SlotsService();
    }

    /**
     * Настройки запросов
     *
     * @return array[]
     */
    public function getDefaultPreFilters(): array
    {
        $postOptions = [
            'prefilters' => [
                new ActionFilter\Csrf(false),
                new ActionFilter\Authentication(false),
                new ActionFilter\HttpMethod([
                    ActionFilter\HttpMethod::METHOD_POST
                ]),
                new ActionFilter\ContentType(['application/json'])
            ],
        ];
        $getOptions = [
            'prefilters' => [
                new ActionFilter\Csrf(false),
                new ActionFilter\Authentication,
                new ActionFilter\HttpMethod([
                    ActionFilter\HttpMethod::METHOD_GET
                ]),
            ]
        ];

        return [
            //'add' => $postOptions,
            //'delete' => $getOptions,
        ];
    }

    /**
     * Создание заявки
     *
     * @return array|AjaxJson
     * * @throws Throwable
     * * @throws BaseExceptionInterface
 */
    public function addAction(): array|AjaxJson
    {
        try {
            $request = $this->request->toArray();

            if(!$this->appointmentService->checkingSlotAvailability($request)) {
                $this->addError(new Error('This slot is not available'));
                return [];
            }

            $result = $this->appointmentService->addAppointment($request);

            if ($result->isSuccess()) {
                return ['id' => $result->getId()];
            } else {
                foreach ($result->getErrors() as $error) {
                    $this->addError(new Error($error->getMessage()));
                }
            }
        } catch (BaseExceptionInterface $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));
        } catch (Throwable $e) {
            //$message = self::makeUnexpectedErrorLogAndGetUniversalMessage('SBKTS_CATEGORY_CONTROLLER_ADD_ACTION', $e);
            //$this->addError(new Error($message, $e->getCode()));
            $this->addError(new Error('Error add'));
        }

        return AjaxJson::createError($this->errorCollection);
    }

    /**
     * Удаление заявки
     *
     * @return array|AjaxJson
     * @throws Throwable
     * @throws BaseExceptionInterface
     */
    public function deleteAction(): array|AjaxJson
    {
        try {
            $request = $this->request->toArray();

            $result = $this->appointmentService->deleteAppointment($request);
            if ($result->isSuccess()) {
                $resultSlot = $this->slotsService->updateSlotAvailability($request);

                if ($resultSlot->isSuccess()) {
                    return ['data' => $result->getData()];
                } else {
                    foreach ($result->getErrors() as $error) {
                        $this->addError(new Error($error->getMessage()));
                    }
                }
            } else {
                foreach ($result->getErrors() as $error) {
                    $this->addError(new Error($error->getMessage()));
                }
            }
        } catch (BaseExceptionInterface $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));
        } catch (Throwable $e) {
            //$message = self::makeUnexpectedErrorLogAndGetUniversalMessage('SBKTS_CATEGORY_CONTROLLER_DELETE_ACTION', $e);
            //$this->addError(new Error($message, $e->getCode()));
            $this->addError(new Error('Error delete'));
        }

        return AjaxJson::createError($this->errorCollection);
    }
}