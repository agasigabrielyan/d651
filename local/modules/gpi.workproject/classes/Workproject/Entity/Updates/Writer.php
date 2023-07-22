<?php
namespace Gpi\Workproject\Entity\Updates;

use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity;

class Writer
{
    public static function addNewRecord($ids, $arFields, $object){

        return;
        
        $id = $object->getId();
        $entity = $object->sysGetEntity();
        $entityName = $entity->getName();

        switch($entityName){

            case 'DetailDirectionDocument':
                
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ActivityDirectionPermissionTable',
                    'PERMISSION_COLUMN' => false,
                    'PERMISSION_COLUMN_VALUE' => false,
                ];
                break;

            case 'DetailDirectionOrder':
                
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ActivityDirectionPermissionTable',
                    'PERMISSION_COLUMN' => false,
                    'PERMISSION_COLUMN_VALUE' => false,
                ];
                break;

            case 'DetailDirectionEvent':
                
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ActivityDirectionPermissionTable',
                    'PERMISSION_COLUMN' => false,
                    'PERMISSION_COLUMN_VALUE' => false,
                ];
                break;

            case 'DetailDirectionImportant':
                
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ActivityDirectionPermissionTable',
                    'PERMISSION_COLUMN' => false,
                    'PERMISSION_COLUMN_VALUE' => false,
                ];
                break;
    
            case 'ForumDiscussionMessage':

                $discussion = Orm\ForumDiscussionTable::getById($arFields['DISCUSSION_ID'])->fetch();

                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ForumUserPermissionTable',
                    'PERMISSION_COLUMN' => 'FORUM_ID',
                    'PERMISSION_COLUMN_VALUE' => $discussion['FORUM_ID'],
                ];
                break;

            case 'ForumDiscussion':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ForumUserPermissionTable',
                    'PERMISSION_COLUMN' => 'FORUM_ID',
                    'PERMISSION_COLUMN_VALUE' => $arFields['FORUM_ID'],
                ];
                break;

            case 'AdItem':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\AdUnionUserPermissionTable',
                    'PERMISSION_COLUMN' => 'UNION_ID',
                    'PERMISSION_COLUMN_VALUE' => $arFields['UNION_ID'],
                ];
                break;

            case 'GalleryAlbumItem':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\GalleryPermissionTable',
                    'PERMISSION_COLUMN' => 'GALLERY_ID',
                    'PERMISSION_COLUMN_VALUE' => $arFields['GALLERY_ID'],
                ];
                break;

            case 'GroupItem':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\GroupItemUserPermissionTable',
                    'PERMISSION_COLUMN' => 'GROUP_ID',
                    'PERMISSION_COLUMN_VALUE' => $id,
                ];
                break;

            case 'ProjectDirection':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\ProjectUserPermissionTable',
                    'PERMISSION_COLUMN' => 'PROJECT_ID',
                    'PERMISSION_COLUMN_VALUE' => $arFields['PROJECT_ID'],
                ];
                break;

            case 'ProjectTaskItems':
                return true;
                break;

            case 'StorageObject':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\StorageUserPermissionTable',
                    'PERMISSION_COLUMN' => 'STORAGE_ID',
                    'PERMISSION_COLUMN_VALUE' => $arFields['STORAGE_ID'],
                ];
                break;

            case 'CalendarEvent':
                $config = [
                    'PREMISSION_TABLE' => 'Gpi\Workproject\Orm\CalendarUserPermissionTable',
                    'PERMISSION_COLUMN' => 'CALENDAR_ID',
                    'PERMISSION_COLUMN_VALUE' => $arFields['CALENDAR_ID'],
                ];
                break;
                
            default:
                return true;
                break;
        }

        
        $usersList = Entity\EditorManager::getEntityElementAllowedUser($config['PREMISSION_TABLE'], $config['PERMISSION_COLUMN_VALUE'], $config['PERMISSION_COLUMN']);

        
        $issetEntity = Orm\EntityUpdateTable::getList([
            'filter' => [
                'ENTITY_ID' => $id,
                'ENTITY_TYPE' => $entityName
            ]
        ])->fetch();

        global $USER;
        $userId = $USER->getId();
        /*$key = array_search($userId, $usersList);
        if($key>0 || ($key == 0 && $usersList[0] == $userId))
            unset($usersList[$key]);*/


        if($issetEntity)
            Orm\EntityUpdateTable::update($issetEntity['ID'], [
                'USERS' => $usersList,
                'ENTITY_ID' => $id,
                'ENTITY_TYPE' => $entityName
            ]);
        else
            Orm\EntityUpdateTable::add([
                'USERS' => $usersList,
                'ENTITY_ID' => $id,
                'ENTITY_TYPE' => $entityName
            ]);
    }

    public static function deleteRecord($id){
        return;
        
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $className = $request['tableName']::getObjectClassName();
        $entityName = substr($className, 3, strlen($className));

        $issetEntities = Orm\EntityUpdateTable::getList([
            'filter' => [
                'ENTITY_ID' => $id,
                'ENTITY_TYPE' => $entityName
            ]
        ]);
        while($issetEntity = $issetEntities->fetch()){
            Orm\EntityUpdateTable::delete($issetEntity['ID']);
        }
    }

    public static function unsetUserNews($updates){
        global $USER;
        $userId = $USER->getId();


        $updates = Orm\EntityUpdateTable::getList([
            'filter' => [
                'ID' => $updates
            ]
        ])->fetchAll();



        foreach($updates as $update){
            $key = array_search($userId, $update['USERS']);
            if($key>0 || ($key == 0 && $update['USERS'][0] == $userId))
                unset($update['USERS'][$key]);

            if(count($update['USERS']) == 0)
                Orm\EntityUpdateTable::delete($update['ID']);
            else if(count($update['USERS']) > 0)
                Orm\EntityUpdateTable::update($update['ID'], [
                    'USERS' => $update['USERS']
                ]);
        }
    }
}
