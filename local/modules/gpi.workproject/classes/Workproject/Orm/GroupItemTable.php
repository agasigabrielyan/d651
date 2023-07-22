<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\Datetime,
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Gpi\Workproject\Orm\Helper;

Loc::loadMessages(__FILE__);

class GroupItemTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rs_group_item';
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
            new Entity\IntegerField('UNION_ID'),
            new Entity\IntegerField('DIRECTION'),

            new Entity\IntegerField('FORUM_ID'),
            new Entity\IntegerField('STORAGE_ID'),
            new Entity\IntegerField('AD_UNION_ID'),
            new Entity\IntegerField('TASKS_UNION_ID'),
            new Entity\IntegerField('CALENDAR_ID'),
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

        $calendarAd = CalendarTable::add(['TITLE' => "Календарь группы"]);
        $forumAd = ForumTable::add(['TITLE' => "Форум группы"]);
        $storageAd = StorageTable::add(['TITLE' => "Диск группы"]);
        $adsAd = AdUnionTable::add(['TITLE' => "Объявления группы"]);

        $arFields['CALENDAR_ID'] = $calendarAd->getId();
        $arFields['FORUM_ID'] = $forumAd->getId();
        $arFields['STORAGE_ID'] = $storageAd->getId();
        $arFields['AD_UNION_ID'] = $adsAd->getId();


        $result->modifyFields($arFields);
        return $result;
    }

    public static function onAfterAdd(Entity\Event $event)
    {

        $id = $event->getParameter("id");
        $arFields = $event->getParameter("fields");

        global $USER;
        $userId = $USER->getId();
        $project = ProjectTable::getList(['filter' => ['GROUP_UNION_ID' => $arFields['UNION_ID']]])->fetch();
        $direction = ProjectDirectionTable::getById($arFields['DIRECTION'])->fetch();

        self::setEntitiesPermission($id, $arFields, 'U_'.$userId, 'X');
        self::setEntitiesPermission($id, $arFields, 'PG_'.$id, 'W');
        ProjectTable::setEntitiesPermission($project['ID'], $project, 'PG_'.$id, 'W');
        ProjectUserTable::addWithCheck([
            'USER_ID' => $userId,
            'PROJECT_ID' => $project['ID'],
            'GROUPS' => [$id],
        ]);

        if($userId != $arFields['DIRECTOR_ID']){
            ProjectUserTable::addWithCheck([
                'USER_ID' => $arFields['DIRECTOR_ID'],
                'PROJECT_ID' => $project['ID'],
                'GROUPS' => [$id],
            ]);
            self::setEntitiesPermission($id, $arFields, 'U_'.$arFields['DIRECTOR_ID'], 'X');
        }

        if($project['DIRECTOR_ID'] != $userId && $project['DIRECTOR_ID'] != $arFields['DIRECTOR_ID'])
            self::setEntitiesPermission($id, $arFields, 'U_'.$project['DIRECTOR_ID'], 'X');

        if($project['CREATED_BY'] != $userId && $project['CREATED_BY'] != $arFields['DIRECTOR_ID'])
            self::setEntitiesPermission($id, $arFields, 'U_'.$project['CREATED_BY'], 'X');

        if($direction['DIRECTOR_ID'] != $userId && $direction['DIRECTOR_ID'] != $arFields['DIRECTOR_ID'] &&
            $direction['DIRECTOR_ID'] != $project['DIRECTOR_ID']){
            ProjectUserTable::addWithCheck([
                'USER_ID' => $direction['DIRECTOR_ID'],
                'PROJECT_ID' => $project['ID'],
                'GROUPS' => [$id],
            ]);
            self::setEntitiesPermission($id, $arFields, 'U_'.$direction['DIRECTOR_ID'], 'X');
        }


        if($direction['CREATED_BY'] != $userId && $direction['CREATED_BY'] != $arFields['CREATED_BY']&&
            $direction['CREATED_BY'] != $project['CREATED_BY']){
            ProjectUserTable::addWithCheck([
                'USER_ID' => $direction['CREATED_BY'],
                'PROJECT_ID' => $project['ID'],
                'GROUPS' => [$id],
            ]);
            self::setEntitiesPermission($id, $arFields, 'U_'.$direction['CREATED_BY'], 'X');
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
        $project = ProjectTable::getList(['filter' => ['GROUP_UNION_ID' => $arFields['UNION_ID']]])->fetch();
        $directions = ProjectDirectionTable::getList(['filter' => ['ID' => $arFields['DIRECTION']]])->fetchAll();

        foreach (array_merge([$project] , $directions) as $data){
            //$xUsers[$data['CREATED_BY']] = $data['CREATED_BY'];
            $xUsers[$data['DIRECTOR_ID']] = $data['DIRECTOR_ID'];
        }

        if($oldData['DIRECTOR_ID'] != $arFields['DIRECTOR_ID'] && $arFields['DIRECTOR_ID']){
            self::setEntitiesPermission($id, $oldData, 'U_'.$arFields['DIRECTOR_ID'], 'X');
            if(!$xUsers[$oldData['DIRECTOR_ID']])
                self::setEntitiesPermission($id, $oldData, 'U_'.$oldData['DIRECTOR_ID'], 'W');
        }

        return $result;
    }

    public static function onBeforeDelete(Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $data = self::getById($id)->fetch();
        $project = ProjectTable::getList(['filter' => ['GROUP_UNION_ID' => $data['UNION_ID']]])->fetch();

        CalendarTable::delete($data['CALENDAR_ID']);
        ForumTable::delete($data['FORUM_ID']);
        StorageTable::delete($data['STORAGE_ID']);
        AdUnionTable::delete($data['AD_UNION_ID']);

        $userList = GroupItemUserPermissionTable::getList(['select' => ['ID'], 'filter' => ['GROUP_ID' => $id]]);
        while($user = $userList->fetch()){
            GroupItemUserPermissionTable::delete($user['ID']);
        }

        self::unsetEntitiesPermissions($id, $data, 'PG_'.$id);
        ProjectTable::unsetEntitiesPermissions($project['ID'], $project, 'PG_'.$id);
    }



    public static function setEntitiesPermission($groupId, $group, $entity, $permission){
        if(!$group)
            $group = self::getById($groupId)->fetch();


        GroupItemUserPermissionTable::addWithCheck([
            'GROUP_ID' => $groupId,
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);

        CalendarUserPermissionTable::addWithCheck([
            'CALENDAR_ID' => $group['CALENDAR_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
        ForumUserPermissionTable::addWithCheck([
            'FORUM_ID' => $group['FORUM_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
        AdUnionUserPermissionTable::addWithCheck([
            'UNION_ID' => $group['AD_UNION_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
        StorageUserPermissionTable::addWithCheck([
            'STORAGE_ID' => $group['STORAGE_ID'],
            'ENTITY' => $entity,
            'PERMISSION' => $permission,
        ]);
    }

    public static function unsetEntitiesPermissions($groupId, $group, $entity){
        if(!$group)
            $group = self::getById($groupId)->fetch();

        $groupEntityPermissions = GroupItemUserPermissionTable::getList([
            'filter' => [
                'GROUP_ID' => $groupId,
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($groupEntityPermission = $groupEntityPermissions->fetch())
            GroupItemUserPermissionTable::delete($groupEntityPermission['ID']);


        $calendarEntityPermissions = CalendarUserPermissionTable::getList([
            'filter' => [
                'CALENDAR_ID' => $group['CALENDAR_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($calendarEntityPermission = $calendarEntityPermissions->fetch())
            CalendarUserPermissionTable::delete($calendarEntityPermission['ID']);


        $forumEntityPermissions = ForumUserPermissionTable::getList([
            'filter' => [
                'FORUM_ID' => $group['FORUM_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($forumEntityPermission = $forumEntityPermissions->fetch())
            ForumUserPermissionTable::delete($forumEntityPermission['ID']);

        $adUnionEntityPermissions = AdUnionUserPermissionTable::getList([
            'filter' => [
                'UNION_ID' => $group['AD_UNION_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($adUnionEntityPermission = $adUnionEntityPermissions->fetch())
            AdUnionUserPermissionTable::delete($adUnionEntityPermission['ID']);


        $storageEntityPermissions = StorageUserPermissionTable::getList([
            'filter' => [
                'STORAGE_ID' => $group['STORAGE_ID'],
                'ENTITY' => $entity,
            ],
            'select' => ['ID']
        ]);
        while($storageEntityPermission = $storageEntityPermissions->fetch())
            StorageUserPermissionTable::delete($storageEntityPermission['ID']);
    }
}