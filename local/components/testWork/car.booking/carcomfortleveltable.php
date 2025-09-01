<?php

namespace MyClass;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * Class LevelTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_EMPLOYEE text optional
 * <li> UF_NAME text optional
 * </ul>
 *
 * @package Bitrix\Comfort
 **/
class CarComfortLevelLevelTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'car_comfort_level';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle(Loc::getMessage('LEVEL_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
            ,
            'UF_EMPLOYEE' => (new TextField('UF_EMPLOYEE',
                []
            ))->configureTitle(Loc::getMessage('LEVEL_ENTITY_UF_EMPLOYEE_FIELD'))
            ,
            'UF_NAME' => (new TextField('UF_NAME',
                []
            ))->configureTitle(Loc::getMessage('LEVEL_ENTITY_UF_NAME_FIELD'))
            ,
        ];
    }
}