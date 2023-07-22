<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class EntityUpdateTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_entity_update';
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
            new Entity\TextField('ENTITY_TYPE'),
            new Entity\IntegerField('ENTITY_ID'),
            new Entity\TextField('USERS',[
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            ]),
            new Entity\ReferenceField(
                'USER',
                'Gpi\Workproject\Orm\EntityUpdateUserTable',
                [
                    'this.ID' => 'ref.ENTITY_UPDATE_ID'
                ]
            )
        ];
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;
        if($arFields['USERS']){
            $arFields['USERS'] = serialize($arFields['USERS']);
            $result->modifyFields($arFields);
        }

        return $result;
    }

    public static function onBeforeUpdate(Entity\Event $event)
    {
        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;
        if($arFields['USERS']){
            $arFields['USERS'] = serialize($arFields['USERS']);
            $result->modifyFields($arFields);
        }

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $arFields = $event->getParameter("fields");

        if($users = unserialize($arFields['USERS'])){
            Helper::setColumnShadowDate('Gpi\Workproject\Orm\EntityUpdateUserTable', 'ENTITY_UPDATE_ID', $id, $users);
        }
    }

    public static function onAfterUpdate(Entity\Event $event)
    {
        $id = $event->getParameter("id")['ID'];
        $arFields = $event->getParameter("fields");

        if($users = unserialize($arFields['USERS'])){
            Helper::setColumnShadowDate('Gpi\Workproject\Orm\EntityUpdateUserTable', 'ENTITY_UPDATE_ID', $id, $users);
        }

    }

    public static function onAfterDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id")['ID'];
        Helper::unsetColumnShadowDate('Gpi\Workproject\Orm\EntityUpdateUserTable', 'ENTITY_UPDATE_ID', $id);
    }
}