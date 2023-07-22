<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\Datetime,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper;

class ActivityDirectionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_acivity_direction';
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
            new Entity\TextField('DESCRIPTION',['nullable' => true]),
            new Entity\TextField('IMPORTANT_TEXT',['nullable' => true]),
            new Entity\TextField('IMPORTANT_DESCRIPTION',['nullable' => true]),
            new Entity\IntegerField('SORT',['nullable' => true]),
            new Entity\TextField('CURATORS', [
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                },
                'nullable' => true
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
        if($arFields['CURATORS'])
            $arFields['CURATORS'] = serialize($arFields['CURATORS']);

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
        if($arFields['CURATORS'])
            $arFields['CURATORS'] = serialize($arFields['CURATORS']);

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");

        $directionsRS = DetailDirectionTable::getList([
            'filter' => ['ACTIVITY_DIRECTION_ID' => $id]
        ]);
        while($direction = $directionsRS->fetch()){
            DetailDirectionTable::delete($direction['ID']);
        }
    }
}