<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\ExpressionField;

Loc::loadMessages(__FILE__);

class GalleryAlbumItemTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_gallery_album_photos';
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
            new Entity\IntegerField('ALBUM_ID'),
            new Entity\IntegerField('FILE'),
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
        $arFields['UPDATED_BY'] = $USER->getId();
        $arFields['CREATED_BY'] = $USER->getId();
        $arFields['UPDATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());
        $arFields['CREATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());

        if(is_array($arFields['FILE']))
            $arFields['FILE'] = \CFile::SaveFile($arFields['FILE'], 'workproject');

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
        if(is_array($arFields['FILE']))
            $arFields['FILE'] = \CFile::SaveFile($arFields['FILE'], 'workproject');

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();
        \CFile::delete($data['FILE']);
    }
}