<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

class ProjectUserPermissionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_proejct_user_permission';
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
            new Entity\IntegerField('PROJECT_ID'),
            new Entity\StringField('ENTITY'),
            new Entity\StringField('PERMISSION'),
        ];
    }

    public static function addWithCheck($arFields){

        if($arFields['ENTITY']){
            $oldPermissions = self::getList([
                'filter' =>  [
                    'PROJECT_ID' => $arFields['PROJECT_ID'],
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