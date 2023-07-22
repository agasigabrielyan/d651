<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\Datetime,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper;

Loc::loadMessages(__FILE__);

class AdItemTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_ad_item';
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
            new Entity\TextField('DESCRIPTION'),
            new Entity\IntegerField('PREVIEW'),
            new Entity\IntegerField('UNION_ID'),
            new Entity\DatetimeField('DATE', ['nullable' => true]),
            new Entity\StringField('FILES', [
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
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
                $arFields['FILES'][$key] = \CFile::SaveFile($file, 'workproject', 'workproject');
            }
            $arFields['FILES'] = serialize($arFields['FILES']);
        }
        if($arFields['PREVIEW'] && is_array($arFields['PREVIEW'])){
            $arFields['PREVIEW'] = \CFile::SaveFile($arFields['PREVIEW'], 'workproject');
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
        }if($arFields['PREVIEW'] && is_array($arFields['PREVIEW'])){
            $arFields['PREVIEW'] = \CFile::SaveFile($arFields['PREVIEW'], 'workproject');
        }

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        global $USER;
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();

        $files = array_merge($data['FILES'] ?? [], [$data['PREVIEW']]);

        foreach ($files as $fileId){
            \CFile::delete($fileId);
        }
    }
}