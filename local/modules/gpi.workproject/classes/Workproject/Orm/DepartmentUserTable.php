<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class DepartmentUserTable extends Entity\DataManager
{
    CONST AUTHOR_ROLE = 'A';
    CONST AUTHOR_DIRECTOR_ROLE = 'W';
    CONST AUTHOR_MODERATOR_ROLE = 'E';
    CONST AUTHOR_USER_ROLE = 'K';
    CONST AUTHOR_BAN_ROLE = 'T';
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_departments_user';
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
            new Entity\StringField('DEPARTMENT_ID'),
            new Entity\StringField('ENTITY_ID'),
            new Entity\EnumField('PERMISSION',[
                'values' => [
                    self::AUTHOR_ROLE => 'Создатель',
                    self::AUTHOR_DIRECTOR_ROLE => 'Руководитель',
                    self::AUTHOR_MODERATOR_ROLE => 'Помощник руководителя',
                    self::AUTHOR_USER_ROLE => 'Участник',
                    self::AUTHOR_BAN_ROLE => 'Заблокирован',
                ]
            ]),
        ];
    }
}