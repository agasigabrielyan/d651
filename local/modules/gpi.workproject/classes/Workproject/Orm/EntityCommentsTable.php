<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class EntityCommentsTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_entity_comments';
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
            new Entity\StringField('BODY'),
            new Entity\StringField('FILES', [
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return array_filter(unserialize($value));
                        }
                    );
                }
            ]),
            new Entity\StringField('ENTITY'),
            new Entity\StringField('ENTITY_ID'),
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
        global $USER;
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();

        foreach ($data['FILES'] as $fileId){
            \CFile::delete($fileId);
        }
    }
}