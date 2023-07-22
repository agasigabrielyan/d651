<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;


class ProjectUserCategoryTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'rs_project_user_category';
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
            new Entity\IntegerField('VALUE_ID'),
            new Entity\TextField('VALUE'),
        ];
    }
}