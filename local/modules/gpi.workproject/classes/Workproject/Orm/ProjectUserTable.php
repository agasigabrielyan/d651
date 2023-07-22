<?php
namespace Gpi\Workproject\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity;


class ProjectUserTable extends Entity\DataManager
{
    const CATEGORY = [
        1 => 'Участник',
        2 => 'Эксперт',
        3 => 'Руководитель группы',
        4 => 'Руководитель проектного направления',
        5 => 'Руководитель проекта',
        6 => 'Администратор',
    ];

    const CATEGORY_SHORT = [
        1 => 'Участник',
        2 => 'Эксперт',
        3 => 'РГ',
        4 => 'РПН',
        5 => 'РП',
        6 => 'Админ',
    ];

    const EDITED_CATEGORIES = [
        1,
        2,
    ];

     public static function getTableName()
    {
        return 'rs_project_user';
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
            new Entity\IntegerField('USER_ID'),
            new Entity\TextField('PROJECT_ID'),
            new Entity\TextField('CATEGORY',[
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            ]),
            new Entity\TextField('GROUPS',[
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            ]),
        ];
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;

        if($arFields['GROUPS'])
            $arFields['GROUPS'] = serialize($arFields['GROUPS']);

        if($arFields['CATEGORY'])
            $arFields['CATEGORY'] = serialize($arFields['CATEGORY']);


        $result->modifyFields($arFields);

        return $result;
    }

    public static function onAfterAdd(Entity\Event $event){
        $id = $event->getParameter("id");
        $arFields = $event->getParameter("fields");

        Helper::setColumnShadowDate('Gpi\Workproject\Orm\ProjectUserGroupsTable', 'VALUE_ID', $id, unserialize($arFields['GROUPS']));
        Helper::setColumnShadowDate('Gpi\Workproject\Orm\ProjectUserCategoryTable', 'VALUE_ID', $id, unserialize($arFields['CATEGORY']));
    }

    public static function onBeforeUpdate(Entity\Event $event)
    {
        $arFields = $event->getParameter("fields");
        $result = new Entity\EventResult;

        if($arFields['GROUPS'])
            $arFields['GROUPS'] = serialize($arFields['GROUPS']);

        if($arFields['CATEGORY'])
            $arFields['CATEGORY'] = serialize($arFields['CATEGORY']);


        $result->modifyFields($arFields);

        return $result;
    }

    public static function onAfterUpdate(Entity\Event $event){
        $id = $event->getParameter("id");
        $arFields = $event->getParameter("fields");

        Helper::setColumnShadowDate('Gpi\Workproject\Orm\ProjectUserGroupsTable', 'VALUE_ID', $id, unserialize($arFields['GROUPS']));
        Helper::setColumnShadowDate('Gpi\Workproject\Orm\ProjectUserCategoryTable', 'VALUE_ID', $id, unserialize($arFields['CATEGORY']));
    }

    public static function onAfterDelete(Entity\Event $event){

        $id = $event->getParameter("id");
        Helper::unsetColumnShadowDate('Gpi\Workproject\Orm\ProjectUserGroupsTable', 'VALUE_ID', $id);
        Helper::unsetColumnShadowDate('Gpi\Workproject\Orm\ProjectUserCategoryTable', 'VALUE_ID', $id);
    }

    public static function addWithCheck($arFields){
        $data = self::getList([
            'filter' => [
                'USER_ID' => $arFields['USER_ID'],
                'PROJECT_ID' => $arFields['PROJECT_ID'],
            ]
        ])->fetch();

        if(!$data){
            self::add($arFields);
            self::correctUserCategory($arFields['USER_ID'], $arFields['PROJECT_ID']);
            return;
        }

        $data['GROUPS'] = array_unique(array_merge($data['GROUPS'], $arFields['GROUPS']));
        $id = $data['ID'];
        unset($data['ID']);
        self::update($id, $data);
        self::correctUserCategory($arFields['USER_ID'], $arFields['PROJECT_ID']);
    }

    public static function correctUserCategory($userId, $projectId){
        $project = ProjectTable::getById($projectId)->fetch();

        $directorGroups = GroupItemTable::getList([
            'filter' => [
                'UNION_ID' => $project['GROUP_UNION_ID'],
                'DIRECTOR_ID' => $userId,
            ],
        ])->fetchAll();

        $directorDirections = ProjectDirectionTable::getList([
            'filter' => [
                'PROJECT_ID' => $projectId,
                'DIRECTOR_ID' => $userId,
            ],
        ])->fetchAll();

        $categories = [];
        if($project['CREATED_BY'] == $userId)
            $categories[]=6;

        if($project['DIRECTOR_ID'] == $userId)
            $categories[]=5;

        if($directorDirections)
            $categories[]=4;

        if($directorGroups)
            $categories[]=3;

        $oldData = self::getList([
            'filter' => [
                'USER_ID' => $userId,
                'PROJECT_ID' => $projectId,
            ]
        ])->fetch();

        if($categories == $oldData['CATEGORY'])
            return;

        $consts = array_diff(array_keys(self::CATEGORY), self::EDITED_CATEGORIES);

        if($oldData['CATEGORY'])
            $categories = array_merge($categories, array_diff($oldData['CATEGORY'], $consts));
        rsort($categories);

        self::update($oldData['ID'], ['CATEGORY' => $categories]);

    }
}