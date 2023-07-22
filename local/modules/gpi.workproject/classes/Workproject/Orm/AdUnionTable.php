<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity,
    Gpi\Workproject\Entity as WorkEntity;

Loc::loadMessages(__FILE__);

class AdUnionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_ad_union';
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
            new Entity\StringField('TITLE')
        ];
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        global $USER;

        $result = new Entity\EventResult;
        $result->modifyFields([
            'UPDATED_BY' => $USER->getId(),
            'CREATED_BY' => $USER->getId(),
            'UPDATED_TIME' => \Bitrix\Main\Type\Datetime::createFromTimestamp(time()),
            'CREATED_TIME' => \Bitrix\Main\Type\Datetime::createFromTimestamp(time()),
        ]);

        return $result;
    }

    public static function onBeforeUpdate(Entity\Event $event)
    {
        global $USER;

        $result = new Entity\EventResult;
        $result->modifyFields([
            'UPDATED_BY' => $USER->getId(),
            'UPDATED_TIME' => \Bitrix\Main\Type\Datetime::createFromTimestamp(time()),
        ]);

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event)
    {
        global $USER;
        $id = $event->getParameter("id");


        AdUnionUserPermissionTable::add([
            'UNION_ID' => $id,
            'ENTITY' => 'U_'.$USER->getId(),
            'PERMISSION' => 'X',
        ]);
    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $adList = AdItemTable::getList(['select' => ['ID'], 'filter' => ['UNION_ID' => $id]]);
        while($ad = $adList->fetch()){
            AdItemTable::delete($ad['ID']);
        }

        $userList = AdUnionUserPermissionTable::getList(['select' => ['ID'], 'filter' => ['UNION_ID' => $id]]);
        while($user = $userList->fetch()){
            AdUnionUserPermissionTable::delete($user['ID']);
        }
    }
}