<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class GalleryTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_gallery';
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


        GalleryPermissionTable::add([
            'GALLERY_ID' => $id,
            'ENTITY' => 'U_'.$USER->getId(),
            'PERMISSION' => 'X',
        ]);
    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $discList = GalleryAlbumTable::getList(['select' => ['ID'], 'filter' => ['GALLERY_ID' => $id]]);
        while($disc = $discList->fetch()){
            GalleryAlbumTable::delete($disc['ID']);
        }

        $permissionList = GalleryPermissionTable::getList(['select' => ['ID'], 'filter' => ['GALLERY_ID' => $id]]);
        while($permission = $permissionList->fetch()){
            GalleryPermissionTable::delete($permission['ID']);
        }
    }
}