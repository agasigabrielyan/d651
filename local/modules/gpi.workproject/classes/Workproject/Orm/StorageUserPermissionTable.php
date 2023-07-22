<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

class StorageUserPermissionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_storage_user_permission';
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
            new Entity\IntegerField('STORAGE_ID'),
            new Entity\StringField('ENTITY'),
            new Entity\StringField('PERMISSION'),
        ];
    }

    public static function addWithCheck($arFields){

        if($arFields['ENTITY']){
            $oldPermissions = self::getList([
                'filter' => [
                    'STORAGE_ID' => $arFields['STORAGE_ID'],
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