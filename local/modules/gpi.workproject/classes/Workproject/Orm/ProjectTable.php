<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\Datetime,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper;

Loc::loadMessages(__FILE__);

class ProjectTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_project';
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
            new Entity\TextField('DESCRIPTION'),
            new Entity\TextField('TARGET'),
            new Entity\IntegerField('TASKS_UNION_ID'),
            new Entity\IntegerField('CALENDAR_ID'),
            new Entity\IntegerField('FORUM_ID'),
            new Entity\IntegerField('STORAGE_ID'),
            new Entity\IntegerField('AD_UNION_ID'),
            new Entity\IntegerField('GROUP_UNION_ID'),
            new Entity\IntegerField('DIRECTOR_ID'),
            new Entity\IntegerField('PUBLIC_GROUP_ID'),
            new Entity\IntegerField('PUBLIC_DIRECTION_ID'),
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

        $calendarAd = CalendarTable::add(['TITLE' => "Календарь проекта"]);
        $forumAd = ForumTable::add(['TITLE' => "Форум проекта"]);
        $storageAd = StorageTable::add(['TITLE' => "Диск проекта"]);
        $adsAd = AdUnionTable::add(['TITLE' => "Объявления проекта"]);
        $groupAd = GroupUnionTable::add(['TITLE' => "Группы проекта"]);

        $arFields['CALENDAR_ID'] = $calendarAd->getId();
        $arFields['FORUM_ID'] = $forumAd->getId();
        $arFields['STORAGE_ID'] = $storageAd->getId();
        $arFields['AD_UNION_ID'] = $adsAd->getId();
        $arFields['GROUP_UNION_ID'] = $groupAd->getId();

        $result->modifyFields($arFields);

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event)
    {
        global $USER;

        $id = $event->getParameter("id");
        $arFields = $event->getParameter("fields");
        $userId = $USER->getId();
        $arFields['ID'] = $id;

        $directionAd = ProjectDirectionTable::add([
            'TITLE' => 'Общее направление',
            'DIRECTOR_ID' => $arFields['DIRECTOR_ID'],
            'PROJECT_ID' => $id,
        ]);

        $groupItemAd = GroupItemTable::add([
            'TITLE' => 'Общая группа',
            'DIRECTOR_ID' => $arFields['DIRECTOR_ID'],
            'DIRECTION' => $directionAd->getId(),
            'UNION_ID' => $arFields['GROUP_UNION_ID'],
        ]);

        self::update($id, [
            'PUBLIC_GROUP_ID' => $groupItemAd->getId(),
            'PUBLIC_DIRECTION_ID' => $directionAd->getId(),
        ]);

        self::setEntitiesPermission($id, $arFields, 'U_'.$userId, 'X');
        ProjectUserTable::addWithCheck([
            'USER_ID' => $userId,
            'PROJECT_ID' => $id,
            'GROUPS' => [$groupItemAd->getId()],
        ]);
        if($userId != $arFields['DIRECTOR_ID']){
            self::setEntitiesPermission($id, $arFields, 'U_'.$arFields['DIRECTOR_ID'], 'X');
            ProjectUserTable::addWithCheck([
                'USER_ID' => $arFields['DIRECTOR_ID'],
                'PROJECT_ID' => $id,
                'GROUPS' => [$groupItemAd->getId()],
            ]);
        }
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
        if($oldData['DIRECTOR_ID'] != $arFields['DIRECTOR_ID'] && $arFields['DIRECTOR_ID']){
            self::setEntitiesPermission($id, $oldData, 'U_'.$arFields['DIRECTOR_ID'], 'X');
            self::setEntitiesPermission($id, $oldData, 'U_'.$oldData['DIRECTOR_ID'], 'W');
            ProjectUserTable::addWithCheck([
                'USER_ID' => $arFields['DIRECTOR_ID'],
                'PROJECT_ID' => $id,
                'GROUPS' => [$oldData['PUBLIC_GROUP_ID']],
            ]);

            $groupList = GroupItemTable::getList(['filter' => ['UNION_ID' => $oldData['GROUP_UNION_ID']]]);
            while($group = $groupList->fetch()){
                GroupItemTable::setEntitiesPermission($group['ID'], $group, 'U_'.$arFields['DIRECTOR_ID'], 'X');
                if($group['DIRECTOR_ID'] != $oldData['DIRECTOR_ID'] && $oldData['DIRECTOR_ID'] != $oldData['CREATED_BY'])
                    GroupItemTable::setEntitiesPermission($group['ID'], $group, 'U_'.$oldData['DIRECTOR_ID'], 'W');
            }
        }

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        global $USER;
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();

        CalendarTable::delete($data['CALENDAR_ID']);
        ForumTable::delete($data['FORUM_ID']);
        StorageTable::delete($data['STORAGE_ID']);
        AdUnionTable::delete($data['AD_UNION_ID']);
        GroupUnionTable::delete($data['GROUP_UNION_ID']);

        $usersList = ProjectUserPermissionTable::getList(['select' => ['ID'], 'filter' => ['PROJECT_ID' => $id]]);
        while($user = $usersList->fetch()){
            ProjectUserPermissionTable::delete($user['ID']);
        }

        $activityDirectionList = ProjectDirectionTable::getList(['select' => ['ID'], 'filter' => ['PROJECT_ID' => $id]]);
        while($direction = $activityDirectionList->fetch()){
            ProjectDirectionTable::delete($direction['ID']);
        }
    }




    public static function setEntitiesPermission($projectId, $project, $entity, $permission){
        if(!$project)
            $project = self::getById($projectId)->fetch();

        ProjectUserPermissionTable::addWithCheck([
            'PROJECT_ID' => $projectId,
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);


        CalendarUserPermissionTable::addWithCheck([
            'CALENDAR_ID' => $project['CALENDAR_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
        ForumUserPermissionTable::addWithCheck([
            'FORUM_ID' => $project['FORUM_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
        AdUnionUserPermissionTable::addWithCheck([
            'UNION_ID' => $project['AD_UNION_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);

        StorageUserPermissionTable::addWithCheck([
            'STORAGE_ID' => $project['STORAGE_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
    }

    public static function unsetEntitiesPermissions($projectId, $project, $entity){
        if(!$project)
            $project = self::getById($projectId)->fetch();

        $projectEntityPermissions = ProjectUserPermissionTable::getList([
            'filter' => [
                'PROJECT_ID' => $project['ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($projectEntityPermission = $projectEntityPermissions->fetch())
            ProjectUserPermissionTable::delete($projectEntityPermission['ID']);


        $calendarEntityPermissions = CalendarUserPermissionTable::getList([
            'filter' => [
                'CALENDAR_ID' => $project['CALENDAR_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($calendarEntityPermission = $calendarEntityPermissions->fetch())
            CalendarUserPermissionTable::delete($calendarEntityPermission['ID']);


        $forumEntityPermissions = ForumUserPermissionTable::getList([
            'filter' => [
                'FORUM_ID' => $project['FORUM_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($forumEntityPermission = $forumEntityPermissions->fetch())
            ForumUserPermissionTable::delete($forumEntityPermission['ID']);

        $adUnionEntityPermissions = AdUnionUserPermissionTable::getList([
            'filter' => [
                'UNION_ID' => $project['AD_UNION_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($adUnionEntityPermission = $adUnionEntityPermissions->fetch())
            AdUnionUserPermissionTable::delete($adUnionEntityPermission['ID']);


        $storageEntityPermissions = StorageUserPermissionTable::getList([
            'filter' => [
                'STORAGE_ID' => $project['STORAGE_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($storageEntityPermission = $storageEntityPermissions->fetch())
            StorageUserPermissionTable::delete($storageEntityPermission['ID']);
    }
}