<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class CalendarTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_calendar';
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

        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;

        $timeNow = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());
        $userId = $USER->getId();
        $arFields['UPDATED_BY'] = $userId;
        $arFields['CREATED_BY'] = $userId;
        $arFields['UPDATED_TIME'] = $timeNow;
        $arFields['CREATED_TIME'] = $timeNow;

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


        CalendarUserPermissionTable::add([
            'CALENDAR_ID' => $id,
            'ENTITY' => 'U_'.$USER->getId(),
            'PERMISSION' => 'X',
        ]);
    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $eventsList = CalendarEventTable::getList(['select' => ['ID'], 'filter' => ['CALENDAR_ID' => $id]]);
        while($event = $eventsList->fetch()){
            CalendarEventTable::delete($event['ID']);
        }

        $userList = CalendarUserPermissionTable::getList(['select' => ['ID'], 'filter' => ['CALENDAR_ID' => $id]]);
        while($user = $userList->fetch()){
            CalendarUserPermissionTable::delete($user['ID']);
        }
    }
}