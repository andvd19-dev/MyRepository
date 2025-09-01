<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\CurrentUser;

Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

class CarBookingComponent extends \CBitrixComponent implements Controllerable, Errorable
{
    protected string $dateTimeStart, $dateTimeEnd;
    protected array $iblockIds = [
        'carBooking' => 7,
        'carUseBooking' => 8,
    ];

    public function configureActions()
    {
        return [];
    }

    public function onPrepareComponentParams($arParams)
    {

        $this->errorCollection = new ErrorCollection();
        $this->dateTimeStart = $this->arResult['DATETIME_START'] = $arParams['DATETIME_START'] ?: '';
        $this->dateTimeEnd = $this->arResult['DATETIME_END'] = $arParams['DATETIME_END'] ?: '';
    }

    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    public function executeComponent()
    {
        $this->arResult['CAR_ACCESS'] = [];
        if (!empty($this->dateTimeEnd) || !empty($this->dateTimeStart)) {
            $this->arResult['CAR_ACCESS'] = $this->getAccessBooking();
        }

        $this->includeComponentTemplate();
    }

    /**
     * Получение доступных бронирований
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getAccessBooking()
    {
        $accessCarForUser = $this->getAccessForUserCars();
        if (empty($accessCarForUser)) {
            return [];
        }

        $weekdays = $this->getWeekday();

        $filter = [
            'IBLOCK_ID' => $this->iblockIds['carBooking'],
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            'PROPERTY_CAR' => array_keys($accessCarForUser),
        ];

        $resCarBooking = \CIBlockElement::GetList([], $filter);

        $result = [];
        //Сбор доступных позиций для пользователя
        //через fetch не получилось сделать, т.к. Он некорректно обрабатывает множественные поля
        while ($car = $resCarBooking->getNextElement()) {
            $prop = $car->GetProperties();
            $fields = $car->getFields();
            $weekdayAccess = $prop['ACCESS']['VALUE_XML_ID'];

            //Проверка, что позиция доступна в выбранные дни пользователя
            if (empty(array_intersect($weekdays, $weekdayAccess))) {
                continue;
            }

            $result[$fields['ID']] = [
                'NAME' => $fields['NAME'],
                'WEEKDAY' => $weekdayAccess,
                'CAR' => $accessCarForUser[$prop['CAR']['VALUE']],
            ];
        }

        $filter = [
            'IBLOCK_ID' => $this->iblockIds['carUseBooking'],
            'PROPERTY_BOOKING' => array_keys($result),
        ];

        $resCarUseBooking = \CIBlockElement::GetList([], $filter, false, false, [
            'PROPERTY_BOOKING',
            'PROPERTY_DATETIME_START',
            'PROPERTY_DATETIME_END',
        ]);

        //Поиск уже забронированных позиций и удалении позиции из массива, если она пересекается с датами пользователя
        while ($car = $resCarUseBooking->fetch()) {
            $timestampStart = strtotime($this->dateTimeStart);
            $timestampEnd = strtotime($this->dateTimeEnd);
            $timestampStartBooking = strtotime($car['PROPERTY_DATETIME_START_VALUE']);
            $timestampEndBooking = strtotime($car['PROPERTY_DATETIME_END_VALUE']);

            if ((!empty($timestampStart) && $timestampStartBooking > $timestampStart && $timestampStartBooking < $timestampEnd) ||
                (!empty($timestampEnd) && $timestampEndBooking > $timestampStart && $timestampEndBooking < $timestampEnd)) {
                unset($result[$car['PROPERTY_BOOKING_VALUE']]);
            }
        }

        return $result;
    }

    /**
     * Получение дней неделей, на которое выпадает бронирование
     *
     * @return array
     */
    private function getWeekday()
    {
        $timestampStart = strtotime($this->dateTimeStart);
        $timestampEnd = strtotime($this->dateTimeEnd);
        $result = [];
        do {
            $result[] = getdate($timestampStart)['weekday'];
            $timestampStart = $timestampStart + 60 * 60 * 24;

        } while (count($result) < 7 && $timestampStart <= $timestampEnd);

        return $result;
    }

    /**
     * Получение доступных машин для бронирования
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getAccessForUserCars()
    {
        require_once __DIR__ . '/positiontable.php';
        require_once __DIR__ . '/carstable.php';
        require_once __DIR__ . '/carcomfortleveltable.php';
        require_once __DIR__ . '/modelcartable.php';

        $rsCars = \Bitrix\Main\UserTable::getList([
            'select' => [
                'UF_POSITION',
                'DRIVER_FIO',
                'CAR_NAME' => 'CARSTABLE.UF_NAME',
                'CAR_COMFORT' => 'CARSTABLE.UF_COMFORT_LEVEL',
                'CAR_COMFORT_NAME' => 'CARCOMFORTLEVELLEVELTABLE.UF_NAME',
                'CAR_MODEL_NAME' => 'MODELCARTABLE.UF_NAME',
                'CAR_MODEL' => 'CARSTABLE.UF_CAR_MODEL',
                'CAR_XML_ID' => 'CARSTABLE.UF_XML_ID',
                'POSITION_ID' => 'POSITIONTABLE.ID',
                'POSITION_NAME' => 'POSITIONTABLE.UF_NAME',
                'DRIVE_USER_ID' => 'CARSTABLE.UF_USER_ID',
            ],
            'filter' => [
                'ID' => CurrentUser::get()->getId(),
            ],
            'runtime' => [
                'POSITIONTABLE' => [
                    'data_type' => '\MyClass\PositionTable',
                    'reference' => ['=ref.ID' => 'this.UF_POSITION'],
                    'join_type' => "LEFT",
                ],
                'CARSTABLE' => [
                    'data_type' => '\MyClass\CarsTable',
                    'reference' => ['=ref.UF_COMFORT_LEVEL' => 'this.POSITION_ID'],
                    'join_type' => "INNER",
                ],
                'CARCOMFORTLEVELLEVELTABLE' => [
                    'data_type' => '\MyClass\CarComfortLevelLevelTable',
                    'reference' => ['=ref.ID' => 'this.CAR_COMFORT'],
                    'join_type' => "INNER",
                ],
                'MODELCARTABLE' => [
                    'data_type' => '\MyClass\ModelCarTable',
                    'reference' => ['=ref.ID' => 'this.CAR_MODEL'],
                    'join_type' => "INNER",
                ],
                'DRIVEUSERTABLE' => [
                    'data_type' => '\Bitrix\Main\UserTable',
                    'reference' => ['=ref.ID' => 'this.DRIVE_USER_ID'],
                    'join_type' => "INNER",
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'DRIVER_FIO',
                    "TRIM(CONCAT(IFNULL(%s,''), ' ', IFNULL(%s,''), ' ', IFNULL(%s,'')))",
                    ['DRIVEUSERTABLE.LAST_NAME', 'DRIVEUSERTABLE.NAME', 'DRIVEUSERTABLE.SECOND_NAME']
                ),
            ],
        ]);

        $carAccess = [];
        while ($car = $rsCars->fetch()) {
            $carAccess[$car['CAR_XML_ID']] = [
                'NAME' => $car['CAR_NAME'],
                'MODEL' => $car['CAR_MODEL_NAME'],
                'COMFORT' => $car['CAR_COMFORT_NAME'],
                'POSITION' => $car['POSITION_NAME'],
                'DRIVER_FIO' => $car['DRIVER_FIO'],
            ];
        }

        return $carAccess;
    }
}
