<?php
namespace MyClass;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * Class Table
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_NAME text optional
 * <li> UF_SORT int optional
 * <li> UF_XML_ID text optional
 * <li> UF_LINK text optional
 * <li> UF_DESCRIPTION text optional
 * <li> UF_FULL_DESCRIPTION text optional
 * <li> UF_DEF int optional
 * <li> UF_FILE int optional
 * <li> UF_USER_ID int optional
 * <li> UF_COMFORT_LEVEL int optional
 * <li> UF_CAR_MODEL int optional
 * </ul>
 *
 * @package Bitrix\
 **/

class CarsTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'cars';
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
            ))->configureTitle(Loc::getMessage('_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
            ,
            'UF_NAME' => (new TextField('UF_NAME',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_NAME_FIELD'))
            ,
            'UF_SORT' => (new IntegerField('UF_SORT',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_SORT_FIELD'))
            ,
            'UF_XML_ID' => (new TextField('UF_XML_ID',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_XML_ID_FIELD'))
            ,
            'UF_LINK' => (new TextField('UF_LINK',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_LINK_FIELD'))
            ,
            'UF_DESCRIPTION' => (new TextField('UF_DESCRIPTION',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_DESCRIPTION_FIELD'))
            ,
            'UF_FULL_DESCRIPTION' => (new TextField('UF_FULL_DESCRIPTION',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_FULL_DESCRIPTION_FIELD'))
            ,
            'UF_DEF' => (new IntegerField('UF_DEF',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_DEF_FIELD'))
            ,
            'UF_FILE' => (new IntegerField('UF_FILE',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_FILE_FIELD'))
            ,
            'UF_USER_ID' => (new IntegerField('UF_USER_ID',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_USER_ID_FIELD'))
            ,
            'UF_COMFORT_LEVEL' => (new IntegerField('UF_COMFORT_LEVEL',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_COMFORT_LEVEL_FIELD'))
            ,
            'UF_CAR_MODEL' => (new IntegerField('UF_CAR_MODEL',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_UF_CAR_MODEL_FIELD'))
            ,
        ];
    }
}