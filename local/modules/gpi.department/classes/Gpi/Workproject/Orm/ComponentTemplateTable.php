<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class ComponentTemplateTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_component_template';
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
            new Entity\StringField('SITE_ID'),
            new Entity\StringField('COMPONENT_NAME'),
            new Entity\StringField('TEMPLATE_NAME'),
        ];
    }
}