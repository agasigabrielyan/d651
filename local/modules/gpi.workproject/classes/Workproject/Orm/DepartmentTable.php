<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class DepartmentTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_department';
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
            new Entity\StringField('SITE_ID', [
                'unique' => 'Y',
            ]),
            new Entity\StringField('PREVIEW_TEXT'),
            new Entity\StringField('DETAIL_TEXT'),
        ];
    }

    public static function onAfterAdd(Entity\Event $event)
    {
        global $USER;
        $id = $event->getParameter("id");


        DepartmentUserTable::add([
            'DEPARTMENT_ID' => $id,
            'ENTITY' => 'U_'.$USER->getId(),
            'PERMISSION' => DepartmentUserTable::AUTHOR_ROLE,
        ]);
    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $userList = DepartmentUserTable::getList(['select' => ['ID'], 'filter' => ['DEPARTMENT_ID' => $id]]);
        while($user = $userList->fetch()){
            DepartmentUserTable::delete($user['ID']);
        }

        $dirList = DepartmentDirectoriesTable::getList(['select' => ['ID'], 'filter' => ['DEPARTMENT_ID' => $id]]);
        while($dir = $dirList->fetch()){
            DepartmentDirectoriesTable::delete($dir['ID']);
        }
    }



    public static function deleteBySiteId($siteId){
        $department = self::getBySiteId($siteId);
        if(!$department)
            return;

        self::delete($department['ID']);
    }

    public static function getBySiteId($siteId){

        $department = self::getList([
            'filter' => ['SITE_ID' => $siteId]
        ])->fetch();

        return $department;
    }
}