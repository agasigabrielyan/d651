<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\Datetime,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper;

class DetailDirectionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_detail_direction';
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
            new Entity\IntegerField('ACTIVITY_DIRECTION_ID'),
            new Entity\IntegerField('SORT',['nullable' => true]),
            new Entity\IntegerField('AD_UNION_ID'),
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

        $adsAd = AdUnionTable::add(['TITLE' => "Объявления направления деятельности {$arFields['TITLE']}"]);
        $arFields['AD_UNION_ID'] = $adsAd->getId();

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

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();

        $documentsRS = DetailDirectionDocumentTable::getList([
            'filter' => ['DETAIL_DIRECTION_ID' => $id]
        ]);
        while($document = $documentsRS->fetch()){
            DetailDirectionDocumentTable::delete($document['ID']);
        }

        $eventsRS = DetailDirectionEventTable::getList([
            'filter' => ['DETAIL_DIRECTION_ID' => $id]
        ]);
        while($event = $eventsRS->fetch()){
            DetailDirectionEventTable::delete($event['ID']);
        }

        $importantsRS = DetailDirectionImportantTable::getList([
            'filter' => ['DETAIL_DIRECTION_ID' => $id]
        ]);
        while($important = $importantsRS->fetch()){
            DetailDirectionImportantTable::delete($important['ID']);
        }

        $ordersRS = DetailDirectionOrderTable::getList([
            'filter' => ['DETAIL_DIRECTION_ID' => $id]
        ]);
        while($order = $ordersRS->fetch()){
            DetailDirectionOrderTable::delete($order['ID']);
        }


        AdUnionTable::delete($data['AD_UNION_ID']);
    }
}