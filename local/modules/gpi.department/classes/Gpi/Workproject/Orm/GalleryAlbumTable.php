<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\ExpressionField;

Loc::loadMessages(__FILE__);

class GalleryAlbumTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_gallery_album';
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
            new Entity\IntegerField('GALLERY_ID'),
            new Entity\IntegerField('CREATED_BY'),
            new Entity\IntegerField('UPDATED_BY'),
            new Entity\DatetimeField('CREATED_TIME'),
            new Entity\DatetimeField('UPDATED_TIME'),
            new Entity\StringField('TITLE'),
            new Entity\StringField('DESCRIPTION'),
            new Entity\IntegerField('PREVIEW',[
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
        if(is_array($arFields['PREVIEW']))
            $arFields['PREVIEW'] = \CFile::SaveFile($arFields['PREVIEW'], 'workproject');

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
        if(is_array($arFields['PREVIEW']))
            $arFields['PREVIEW'] = \CFile::SaveFile($arFields['PREVIEW'], 'workproject');

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();
        \CFile::delete($data['PREVIEW']);
    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $photoList = GalleryAlbumItemTable::getList(['select' => ['ID'], 'filter' => ['ALBUM_ID' => $id]]);
        while($photo = $photoList->fetch()){
            GalleryAlbumItemTable::delete($photo['ID']);
        }
    }
}