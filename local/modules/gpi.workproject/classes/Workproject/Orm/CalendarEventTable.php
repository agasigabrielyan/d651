<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\Datetime,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper;

Loc::loadMessages(__FILE__);

class CalendarEventTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_calendar_event';
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
            new Entity\TextField(
                'TYPE',[
                    'default' => 'EVENT',
                    'values' => ['EVENT', 'TASK', 'WORK']
                ]
            ),
            new Entity\BooleanField(
                'IS_PUBLICK',[
                    'default' => 0,
                    'values' => [1, 0]
                ]
            ),
            new Entity\IntegerField('CREATED_BY'),
            new Entity\IntegerField('UPDATED_BY'),
            new Entity\DatetimeField('CREATED_TIME'),
            new Entity\DatetimeField('UPDATED_TIME'),
            new Entity\TextField('TITLE'),
            new Entity\TextField('DESCRIPTION'),
            new Entity\DatetimeField('STARTED'),
            new Entity\DatetimeField('ENDED'),
            new Entity\IntegerField('CALENDAR_ID',['nullable' => true,]),
            new Entity\DatetimeField('FACT_STARTED',['nullable' => true,]),
            new Entity\DatetimeField('FACT_ENDED',['nullable' => true,]),
            new Entity\IntegerField('GUARANTOR',['nullable' => true,]),
            new Entity\IntegerField('PROVIDER',['nullable' => true,]),
            new Entity\IntegerField('EXECUTOR',['nullable' => true,]),
            new Entity\IntegerField('GUARANTOR',['nullable' => true,]),
            new Entity\IntegerField('PARENT_EVENT_ID',['nullable' => true,]),
            new Entity\StringField('FILES', [
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                },
                'nullable' => true,
            ]),
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

        if($arFields['FILES']){
            foreach (array_filter($arFields['FILES'], fn($v) => is_array($v)) as $key => $file){
                $arFields['FILES'][$key] = \CFile::SaveFile($file, 'rs_calendar');
            }
            $arFields['FILES'] = serialize($arFields['FILES']);
        }

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
        if($arFields['FILES']){
            foreach (array_filter($arFields['FILES'], fn($v) => is_array($v)) as $key => $file){
                $arFields['FILES'][$key] = CFile::SaveFile($file);
            }
            $arFields['FILES'] = serialize($arFields['FILES']);
        }

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        global $USER;
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();

        foreach ($data['FILES'] as $fileId){
            \CFile::delete($fileId);
        }
    }
}