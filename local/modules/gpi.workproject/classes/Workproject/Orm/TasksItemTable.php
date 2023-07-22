<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class TasksItemTable extends Entity\DataManager
{
    const PREORITY = [
        1 => 'Низкий',
        2 => 'Средний',
        3 => 'Высокий',
    ];
    const STATUS = [
        0 => 'Новая',
        1 => 'На утверждении',
        2 => 'На согласовании',
        3 => 'В исполнении',
        4 => 'Просрочена, в исполнении',
        5 => 'Исполнена',
        6 => 'Отклонена',
    ];
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_task_item';
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
            new Entity\StringField('TITLE'),
            new Entity\StringField('DESCRIPTION',['nullable' => true]),
            new Entity\StringField('FILES',[
                'nullable' => true,
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return array_filter(unserialize($value));
                        }
                    );
                }
            ]),
            new Entity\IntegerField('PRODUCER'),
            new Entity\IntegerField('PROVIDER'),
            new Entity\IntegerField('PREORITY',['default' => 1]),
            new Entity\IntegerField('LABOR_COST',['nullable' => true]),
            new Entity\DatetimeField('CONTROL_DATE',['nullable' => true]),
            new Entity\IntegerField('PROJECT_ID'),
            new Entity\IntegerField('GROUP_ID'),
            new Entity\IntegerField('STATUS', [
                'value' => [
                    'Новая',
                    'На утверждении',
                    'На согласовании',
                    'В исполнении',
                    'Иcполнено',
                    'Просрочено',
                    'Отклонено'
                ]
            ]),
            new Entity\IntegerField('PROVIDED_DATE',['nullable' => true]),
            new Entity\IntegerField('IS_IMPORTANT', ['default' => 0]),
            new Entity\IntegerField('IS_CONDITIONAL_APPROVAL', ['default' => 0]),
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
                $arFields['FILES'][$key] = \CFile::SaveFile($file, 'workproject');
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
                $arFields['FILES'][$key] = \CFile::SaveFile($file, 'workproject');
            }
            $arFields['FILES'] = serialize($arFields['FILES']);
        }

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();

        foreach ($data['FILES'] as $fileId){
            \CFile::delete($fileId);
        }
    }
}