<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class TasksItemApprovalTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_task_item_approval';
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
            new Entity\IntegerField('TASK_ID'),
            new Entity\IntegerField('RP_ID'),
            new Entity\IntegerField('RPN_ID'),
            new Entity\IntegerField('RG_ID'),
            new Entity\StringField('RP_APPROVED', ['nullable' => true]),
            new Entity\StringField('RPN_APPROVED', ['nullable' => true]),
            new Entity\StringField('RG_APPROVED', ['nullable' => true]),
            new Entity\DatetimeField('RP_APPROVED_TIME', ['nullable' => true]),
            new Entity\DatetimeField('RPN_APPROVED_TIME', ['nullable' => true]),
            new Entity\DatetimeField('RG_APPROVED_TIME', ['nullable' => true]),
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
}