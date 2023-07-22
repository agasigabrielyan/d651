<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;


class EntityUpdateUserTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'rs_entity_update_user';
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
            new Entity\IntegerField('ENTITY_UPDATE_ID'),
            new Entity\IntegerField('VALUE'),
        ];
    }
}