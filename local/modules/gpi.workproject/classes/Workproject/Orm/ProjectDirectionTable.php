<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class ProjectDirectionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_project_direction';
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
            new Entity\TextField('TARGET'),
            new Entity\TextField('PROJECT_ID'),
            new Entity\IntegerField('DIRECTOR_ID'),
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

        $id = $event->getParameter("id");
        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;
        $arFields['UPDATED_BY'] = $USER->getId();
        $arFields['UPDATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());

        $result->modifyFields($arFields);

        $oldData = self::getById($id)->fetch();
        if($oldData['DIRECTOR_ID'] != $arFields['DIRECTOR_ID']){
            $project = ProjectTable::getById($oldData['PROJECT_ID'])->fetch();
            $groupList = GroupItemTable::getList(['filter' => ['UNION_ID' => $project['GROUP_UNION_ID']]]);
            while($group = $groupList->fetch()){
                GroupItemTable::setEntitiesPermission($group['ID'], $group, 'U_'.$arFields['DIRECTOR_ID'], 'X');
                ProjectUserTable::addWithCheck([
                    'USER_ID' => $arFields['DIRECTOR_ID'],
                    'PROJECT_ID' => $project['ID'],
                    'GROUPS' => [$group['ID']],
                ]);
                if($project['DIRECTOR_ID'] != $oldData['DIRECTOR_ID'] && $group['DIRECTOR_ID'] != $oldData['DIRECTOR_ID'])
                    GroupItemTable::setEntitiesPermission($group['ID'], $group, 'U_'.$oldData['DIRECTOR_ID'], 'W');
            }
        }

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event){
        
        $arFields = $event->getParameter("fields");

        $project = ProjectTable::getById($arFields['PROJECT_ID'])->fetch();
        $groupList = GroupItemTable::getList(['filter' => ['UNION_ID' => $project['GROUP_UNION_ID']]]);
        while($group = $groupList->fetch()){
            if($arFields['DIRECTOR_ID'] != $group['DIRECTOR_ID'])
                GroupItemTable::setEntitiesPermission($group['ID'], $group, 'U_'.$arFields['DIRECTOR_ID'], 'X');
        }
    }
}