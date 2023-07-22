<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class StorageTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_storage';
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
            new Entity\IntegerField('CREATED_BY'),
            new Entity\IntegerField('UPDATED_BY'),
            new Entity\DatetimeField('CREATED_TIME'),
            new Entity\DatetimeField('UPDATED_TIME'),
            new Entity\TextField('TITLE'),
        ];
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        global $USER;


        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;
        $arFields['UPDATED_BY'] = $USER->getId();
        $arFields['CREATED_BY'] = $USER->getId();
        $arFields['UPDATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());
        $arFields['CREATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeUpdate(Entity\Event $event)
    {
        global $USER;

        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;
        $arFields['UPDATED_BY'] = $USER->getId();
        $arFields['UPDATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event)
    {

        global $USER;
        $id = $event->getParameter("id");

        StorageUserPermissionTable::add([
            'STORAGE_ID' => $id,
            'ENTITY' => 'U_'.$USER->getId(),
            'PERMISSION' => 'X',
        ]);
    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $objectList = StorageObjectTable::getList(['select' => ['ID'], 'filter' => ['STORAGE_ID' => $id]]);
        while($object = $objectList->fetch()){
            StorageObjectTable::delete($object['ID']);
        }

        $userList = StorageUserPermissionTable::getList(['select' => ['ID'], 'filter' => ['STORAGE_ID' => $id]]);
        while($user = $userList->fetch()){
            StorageUserPermissionTable::delete($user['ID']);
        }
    }
}