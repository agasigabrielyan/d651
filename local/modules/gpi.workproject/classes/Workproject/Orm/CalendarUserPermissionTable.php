<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

class CalendarUserPermissionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_calendar_user_permission';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new Entity\IntegerField('CALENDAR_ID'),
            new Entity\StringField('ENTITY'),
            new Entity\StringField('PERMISSION'),
        ];
    }

    public static function addWithCheck($arFields){

        if($arFields['ENTITY']){
            $oldPermissions = self::getList([
                'filter' =>  [
                    'CALENDAR_ID' => $arFields['CALENDAR_ID'],
                    'ENTITY' => $arFields['ENTITY'],
                ],
                'select' => ['ID'],
            ]);
            while($oldPermission = $oldPermissions->fetch()){
                self::delete($oldPermission['ID']);
            }
        }

        self::add($arFields);
    }
}