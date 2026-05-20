<?php

use \Bitrix\Main\Loader;
use \Norbit\Appointment\ORM\AppointmentsTable;
class AdminAppointmentsListComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('norbit.appointment');

        $this->checkRequests();

        $gridId = 'appointments_list';
        $filterId = 'appointments_list_filter';

        $references = $this->getReferencesValues();
        $columns = $this->getColumnsForAppointmentsGrid();
        $filter = $this->getFilterForAppointmentsFilter();

        $gridOptions = new Bitrix\Main\Grid\Options($gridId);
        $sort = $gridOptions->GetSorting()['sort'];
        $navParams = $gridOptions->GetNavParams();

        $nav = new Bitrix\Main\UI\PageNavigation($gridId);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $colQuery = AppointmentsTable::query()
            ->addSelect('*')
            ->setOffset($nav->getOffset())
            ->setLimit($nav->getLimit());

//        $res = $colQuery->fetch();
//        print_r($res);

        $filterOptions = new Bitrix\Main\UI\Filter\Options($filterId);
        $filterFields = $filterOptions->getFilter($filter);

        if ($filterFields['FILTER_APPLIED']) {
            $queryFilter = $this->getApplicantFilter($filterFields);
        }

        if (isset($queryFilter)) {
            $colQuery->where($queryFilter);
        }

        if (!empty($sort)) {
            foreach ($sort as $key => $value) {
                $colQuery->addOrder($key, $value);
            }
        }

        $nav->setRecordCount($colQuery->queryCountTotal());
        $col = $colQuery->fetchCollection();

        $rows = [];

        foreach ($col as $item) {
            $rowColumns = [];
            $data = [];
            foreach ($columns as ['id' => $columnCode]) {
                $value = $item->get($columnCode);
                if ($columnCode == 'ACTIVE') {
                    $value = $value ? 'Y' : 'N';
                }elseif ($columnCode == 'SERVICE_ID') {
                    //$value = $this->getValueForFieldWithAppointments($value);
                }elseif ($columnCode == 'DATE') {
                    $value = $value->format('d.m.Y H:i:s');
                }

                if (array_key_exists($columnCode, $references)) {
                    if (is_array($value)) {
                        $arValue = [];
                        foreach ($value as $val) {
                            $arValue[] = $references[$columnCode][$val];
                        }

                        $value = $arValue
                            ? implode(', ', $arValue)
                            : GetMessage('ADMIN_ORDERS_LIST_NOT_SELECTED');
                    } else {
                        $value = $references[$columnCode][$value];
                    }
                }

                $rowColumns[$columnCode] = $value;
                $data[$columnCode] = $value;
            }

            $rowColumns['ACTIVE'] = $rowColumns['ACTIVE'] == 'Y'
                ? GetMessage('ADMIN_APPOINTMENTS_LIST_YES')
                : GetMessage('ADMIN_APPOINTMENTS_LIST_NO');


            $rows[] = [
                'id' => $item->getId(),
                'data' => $data,
                'columns' => $rowColumns,
                'actions' => [
                    [
                        'text' => GetMessage('ADMIN_APPOINTMENTS_LIST_EDIT_APPOINTMENT'),
                        'onclick' => 'location.href="' . $this->arParams['URL_TO_DETAIL'] . '?APPOINTMENT_ID=' . $item->getId() . '"',
                        'default' => true,
                    ],
                    [
                        'text' => GetMessage('ADMIN_APPOINTMENTS_LIST_DELETE_APPOINTMENT'),
                        'onclick' => 'deleteAppointment(' . $item->getId() . ',' . $item->getSlotId() . ')',
                        'default' => true,
                    ],
                ],
            ];
        }

        $this->arResult = [
            'COLUMNS' => $columns,
            'FILTER' => $filter,
            'ROWS' => $rows,
            'NAV' => $nav,
            'FILTER_ID' => $filterId,
            'GRID_ID' => $gridId,
        ];

        $this->includeComponentTemplate();
    }

    private function getColumnsForAppointmentsGrid(): array
    {
        $columns = [];
        foreach (AppointmentsTable::getMap() as $field) {

            $column = [
                'id' => $field->getName(),
                'name' => $field->getTitle(),
                'default' => true,
            ];

            $nonEditableFields = ['ID'];
            $column['editable'] = !in_array($column['id'], $nonEditableFields);
            if ($column['id'] == 'ACTIVE') {
                $column['type'] = 'checkbox';
            }

            $columns[] = $column;
        }

        return $columns;
    }

    private function getFilterForAppointmentsFilter(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => GetMessage('ADMIN_APPOINTMENTS_LIST_ID_FIELD'),
                'type' => 'number'
            ],
            [
                'id' => 'ACTIVE',
                'name' => GetMessage('ADMIN_APPOINTMENTS_LIST_ACTIVE_FIELD'),
                'type' => 'checkbox',
                'default' => true
            ],
        ];
    }


    private function getApplicantFilter(array $filterFields): \Bitrix\Main\ORM\Query\Filter\ConditionTree
    {
        $queryFilter = Bitrix\Main\ORM\Query\Query::filter();

        $additionalOperator = $filterFields['ID_numsel'] == 'more' ? '' : '=';
        if ($filterFields['ID_from'] > 0) {
            $queryFilter->where('ID', '>' . $additionalOperator, $filterFields['ID_from']);
        }

        if ($filterFields['ID_to'] > 0) {
            $queryFilter->where('ID', '<' . $additionalOperator, $filterFields['ID_to']);
        }

        if (!empty($filterFields['NAME'])) {
            $queryFilter->whereLike('NAME', '%' . $filterFields['NAME'] . '%');
        }

        if (!empty($filterFields['CODE'])) {
            $queryFilter->whereLike('CODE', '%' . $filterFields['CODE'] . '%');
        }

        if (!empty($filterFields['SORT'])) {
            $queryFilter->whereLike('SORT', $filterFields['SORT']);
        }

        if (!empty($filterFields['ACTIVE'])) {
            $queryFilter->whereLike('ACTIVE', $filterFields['ACTIVE']);
        }

        return $queryFilter;
    }

    private function checkRequests(): void
    {
        $request = $this->request->toArray();
        if (isset($request['action_button_categories_list']) &&
            $request['action_button_categories_list'] == 'delete' &&
            !empty($request['ID'])
        ) {
            $this->deleteSelectedAppointments($request['ID']);
        } elseif (
            isset($request['action_button_categories_list']) &&
            $request['action_button_categories_list'] == 'edit' &&
            !empty($request['FIELDS'])
        ) {
            $this->updateSelectedRows($request['FIELDS']);
        }
    }

    private function deleteSelectedAppointments(array $arStatusIds)
    {
        foreach ($arStatusIds as $statusId) {
            AppointmentsTable::delete([
                'ID' => $statusId
            ]);
        }
    }

    private function updateSelectedRows(array $arRows)
    {
        foreach ($arRows as $rowId => $arFields) {
            AppointmentsTable::update($rowId, $arFields);
        }
    }

    /**
     * Получаем значения из всех зависимых таблиц, для формирования в колонках их значений вместо ID
     */
    private function getReferencesValues(): array
    {
        $columns = AppointmentsTable::getMap();
        $references = [];
        foreach ($columns as $field) {
            if ($field->getParameter('IS_REFERENCE_ID')) {
                $values = [];
                $resValues = $field->getParameter('REFERENCE_CLASS')::query()
                    ->setSelect(['*'])
                    ->exec();
                while($row = $resValues->fetch()) {
                    if($field->getName() == 'SLOT_ID') {
                        $values[$row['ID']] = $row['DATE'];
                    } else {
                        $values[$row['ID']] = $row['NAME'];
                    }
                }
                $references[$field->getName()] = $values;
            }
        }

        return $references;
    }
}
