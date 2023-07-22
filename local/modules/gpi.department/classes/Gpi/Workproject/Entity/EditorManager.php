<?php
namespace Gpi\Workproject\Entity;

use Gpi\Workproject\Orm;

class EditorManager
{
    const EDIT_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"><title>Редактировать</title> <g id="style=bulk"> <g id="edit"> <path id="vector" d="M21.0618 5.24219L18.9405 3.12087C17.7689 1.94929 15.8694 1.94929 14.6978 3.12087L3.70656 14.1121C3.22329 14.5954 2.91952 15.2292 2.84552 15.9086L2.45151 19.5264C2.31313 20.7969 3.38571 21.8695 4.65629 21.7311L8.27401 21.3371C8.95345 21.2631 9.58725 20.9594 10.0705 20.4761L21.0618 9.48483C22.2334 8.31326 22.2334 6.41376 21.0618 5.24219Z" fill="#FFF"></path> <path id="vector_2" d="M21.0618 5.24219L18.9405 3.12087C17.7689 1.94929 15.8694 1.94929 14.6978 3.12087L12.3644 5.45432L18.7283 11.8183L21.0618 9.48483C22.2334 8.31326 22.2334 6.41376 21.0618 5.24219Z" fill="#000000"></path> </g> </g> </svg>';
    const DELETE_SVG = '<svg xmlns="http://www.w3.org/2000/svg" fill="#FFF" width="800px" height="800px" viewBox="0 0 24 24" id="delete-alt" class="icon glyph"> <title>Удалить</title><path d="M17,4V5H15V4H9V5H7V4A2,2,0,0,1,9,2h6A2,2,0,0,1,17,4Z"></path><path d="M20,6H4A1,1,0,0,0,4,8H5V20a2,2,0,0,0,2,2H17a2,2,0,0,0,2-2V8h1a1,1,0,0,0,0-2ZM11,17a1,1,0,0,1-2,0V11a1,1,0,0,1,2,0Zm4,0a1,1,0,0,1-2,0V11a1,1,0,0,1,2,0Z"></path></svg>';

    const AUTH_USER_RULLS = [
        'OTHER_AUTH' => 'Все авторизованные пользователи',
    ];

    const NOT_AUTH_USER_RULLS = [
        'OTHER_NOT_AUTH' => 'Все не авторизованные пользователи',
    ];

    public static function defineActivityDirectionsPermission(){



        $permissions = [];
        $filter = [
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\ActivityDirectionPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }

        return $permissions;
    }

    public static function defineCalendarPermission($calendarId){



        $permissions = [];
        $filter = [
            'CALENDAR_ID' => $calendarId,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];
        
        $rullsRS = Orm\CalendarUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }

        return $permissions;
    }

    public static function getAlowedCalendarIds(){
        $ids=[];
        $rullsRS = Orm\CalendarUserPermissionTable::getList([
            'filter' => [
                'ENTITY' => EditorManager::getCurrentUserPermissionEntities(),
                '!PERMISSION' => ['D']
            ],
            'group' => ['CALENDAR_ID'],
            'select' => ['CALENDAR_ID'],
        ]);
        while($rull = $rullsRS->fetch()){
            $ids[] = $rull['CALENDAR_ID'];
        }
        return $ids;
    }

    public static function defineAdPermission($union){



        $permissions = [];
        $filter = [
            'UNION_ID' => $union,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\AdUnionUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }

        return $permissions;
    }

    public static function defineForumPermission($forum){



        $permissions = [];
        $filter = [
            'FORUM_ID' => $forum,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\ForumUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function defineStoragePermission($forum){



        $permissions = [];
        $filter = [
            'STORAGE_ID' => $forum,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\StorageUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function defineGalleryPermission($gallery){



        $permissions = [];
        $filter = [
            'GALLERY_ID' => $gallery,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\GalleryPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function getAlowedGalleryIds(){
        $ids=[];
        $rullsRS = Orm\GalleryPermissionTable::getList([
            'filter' => [
                'ENTITY' => EditorManager::getCurrentUserPermissionEntities(),
            ],
            'group' => ['GALLERY_ID'],
            'select' => ['GALLERY_ID'],
        ]);
        while($rull = $rullsRS->fetch()){
            $ids[] = $rull['GALLERY_ID'];
        }
        return $ids;
    }

    public static function defineGroupPermission($group){



        $permissions = [];
        $filter = [
            'GROUP_ID' => $group,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\GroupItemUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function defineGroupListPermission($group){



        $permissions = [];
        $filter = [
            'GROUP_ID' => $group,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\GroupItemUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[$rull['GROUP_ID']][] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function defineProjectPermission($project){



        $permissions = [];
        $filter = [
            'PROJECT_ID' => $project,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\ProjectUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function defineProjectListPermission($project){



        $permissions = [];
        $filter = [
            'PROJECT_ID' => $project,
            'ENTITY' => self::getCurrentUserPermissionEntities()
        ];

        $rullsRS = Orm\ProjectUserPermissionTable::getList([
            'filter' => $filter
        ]);
        while($rull = $rullsRS->fetch()){
            $permissions[$rull['PROJECT_ID']][] = $rull['PERMISSION'];
        }


        return $permissions;
    }

    public static function getCurrentUserGroups(){
        global $USER;
        return array_map(function($v){
            return 'G_'.$v;
        }, \CUser::GetUserGroup($USER->getId()));
    }

    public static function getCurrentUserProjectGroups(){
        global $USER;
        return array_map(function($v){
            return 'PG_'.$v;
        }, array_column(Orm\ProjectUserTable::getList([
                'filter' => ['USER_ID' => $USER->getId()],
                'select' => ['GROUPID' => 'GROUP_IDS.VALUE'],
                'group' => ['GROUP_IDS.VALUE'],
                'runtime' => [
                    'GROUP_IDS' => [
                        'data_type' => 'Gpi\Workproject\Orm\ProjectUserGroupsTable',
                        'reference' => [
                            'this.ID' => 'ref.VALUE_ID'
                        ]
                    ]
                ]
            ])->fetchAll(), 'GROUPID'));
    }

    public static function getCurrentUserPermissionEntities(){
        $entities=[];
        global $USER;
        if($userId = $USER->getId()){
            $entities[] = 'U_'.$userId;
            $entities = array_merge($entities, self::AUTH_USER_RULLS);
            $entities = array_merge($entities, self::getCurrentUserGroups());
            $entities = array_merge($entities, self::getCurrentUserProjectGroups());
        }else
            $entities = array_keys(self::NOT_AUTH_USER_RULLS);
        return $entities;
    }

    public static function getEntityElementAllowedUser($entityPermissionsTable, $elementId=false, $elementColumnName=false){

        $filter = ['PERMISSION' != ['D']];
        if($elementId && $elementColumnName)
            $filter[$elementColumnName] = $elementId;

        $permissions = $entityPermissionsTable::getList([
            'filter' => $filter
        ])->fetchAll();
        $entities = array_column($permissions, 'ENTITY');


        if(!in_array('OTHER_AUTH',$entities)){
            $userEntities = array_filter($entities, fn($v) => strpos($v, 'U_') !== false);
            $groupEntities = array_filter($entities, fn($v) => strpos($v, 'G_') === 0);
            $projecrGroupEntities = array_filter($entities, fn($v) => strpos($v, 'PG_') === 0);

            $userIds = array_map(function ($v){
                return mb_substr($v, 2, strlen($v));
            }, $userEntities);

            $groupIds = array_map(function ($v){
                return mb_substr($v, 2, strlen($v));
            }, $groupEntities);

            $projectGroupIds = array_map(function ($v){
                return mb_substr($v, 3, strlen($v));
            }, $projecrGroupEntities);

            if($groupIds){
                $result = \Bitrix\Main\UserGroupTable::getList(array(
                    'filter' => array('GROUP.ACTIVE'=>'Y', 'GROUP.ID' => $groupIds),
                    'select' => array('USER_ID'),
                ))->fetchAll();

                $userIds = array_unique(array_merge($userIds, array_column($result, 'USER_ID')));
            }

            if($projectGroupIds){
                $result = Orm\ProjectUserTable::getList([
                    'select' => [
                        'USER_ID',
                    ],
                    'filter' => [
                        'GROUP.VALUE' => $projectGroupIds
                    ],
                    'runtime' => [
                        'GROUP' => [
                            'data_type' => 'Gpi\Workproject\Orm\ProjectUserGroupsTable',
                            'reference' => [
                                'this.ID' => 'ref.VALUE_ID',
                            ]
                        ],
                    ]
                ])->fetchAll();

                $userIds = array_unique(array_merge($userIds, array_column($result, 'USER_ID')));
            }
        }else{
            $userIds = array_column( \Bitrix\Main\UserTable::getList(['select' => ['ID']])->fetchAll() , 'ID');
        }

        return $userIds;

    }
}