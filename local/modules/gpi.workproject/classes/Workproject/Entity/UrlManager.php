<?php
namespace Gpi\Workproject\Entity;

class UrlManager
{
    const linkPatherns = [

        "project.list"                 => "",
        "project.list.item"            => "#project_id#/",
        "project.list.item.edit"       => "#project_id#/edit/",
        "project.list.item.settings"   => "#project_id#/settings/",
        "project.locked"               => "#project_id#/locked/",

        "project.discussion.list"      => "#project_id#/discussions/",
        "project.discussion.item"      => "#project_id#/discussions/#theme_id#/",
        "project.discussion.message.edit" => "#project_id#/discussions/#theme_id#/edit/#message_id#/",
        "project.discussion.message.answer" => "#project_id#/discussions/#theme_id#/edit/#message_id#/answer/",
        "project.discussion.item.edit" => "#project_id#/discussions/#theme_id#/edit/",
        "project.discussion.settings"  => "#project_id#/discussions/settings/",

        "project.calendar"             => "#project_id#/calendar/",
        "project.calendar.item"        => "#project_id#/calendar/#event_id#/",
        "project.calendar.item.edit"   => "#project_id#/calendar/#event_id#/edit/",
        "project.calendar.settings"    => "#project_id#/calendar/settings/",

        "project.ad.list"              => "#project_id#/ads/",
        "project.ad.item"              => "#project_id#/ads/#ad_id#/",
        "project.ad.item.edit"         => "#project_id#/ads/#ad_id#/edit/",
        "project.ad.settings"          => "#project_id#/ads/settings/",

        "project.drive"                => "#project_id#/drive/",
        "project.drive.path"           => "#project_id#/drive/#folder_id#/",
        "project.drive.settings"       => "#project_id#/drive/settings/",
        "project.users.update"         => "#project_id#/users/update/",

        "project.tasks"                => "#project_id#/tasks/",
        "project.tasks.list"           => "#project_id#/tasks/#task_id#/",
        "project.tasks.list.item.edit" => "#project_id#/tasks/#task_id#/edit/",

        "project.direction.item"       => "#project_id#/direction/#direction_id#/",

        "project.users"                => "#project_id#/users/",



        "group.list"                   => "#project_id#/groups/",
        "group.list.item"              => "#project_id#/groups/#group_id#/",
        "group.list.item.edit"         => "#project_id#/groups/#group_id#/edit/",
        "group.list.item.settings"     => "#project_id#/groups/#group_id#/settings/",
        "group.locked"                 => "#project_id#/groups/#group_id#/locked/",

        "group.discussion.list"        => "#project_id#/groups/#group_id#/forum/",
        "group.discussion.item"        => "#project_id#/groups/#group_id#/forum/#theme_id#/",
        "group.discussion.item.edit"   => "#project_id#/groups/#group_id#/forum/#theme_id#/edit/",
        "group.discussion.message.edit" => "#project_id#/groups/#group_id#/forum/#theme_id#/edit/#message_id#/",
        "group.discussion.message.answer" => "#project_id#/groups/#group_id#/forum/#theme_id#/edit/#message_id#/answer/",
        "group.discussion.settings"    => "#project_id#/groups/#group_id#/forum/settings/",

        "group.calendar"               => "#project_id#/groups/#group_id#/calendar/",
        "group.calendar.item"          => "#project_id#/groups/#group_id#/calendar/#event_id#/",
        "group.calendar.item.edit"     => "#project_id#/groups/#group_id#/calendar/#event_id#/edit/",
        "group.calendar.settings"      => "#project_id#/groups/#group_id#/calendar/settings/",

        "group.ad.list"                => "#project_id#/groups/#group_id#/ads/",
        "group.ad.item"                => "#project_id#/groups/#group_id#/ads/#ad_id#/",
        "group.ad.item.edit"           => "#project_id#/groups/#group_id#/ads/#ad_id#/edit/",
        "group.ad.settings"            => "#project_id#/groups/#group_id#/ads/settings/",

        "group.drive"                  => "#project_id#/groups/#group_id#/drive/",
        "group.drive.path"             => "#project_id#/groups/#group_id#/drive/#folder_id#/",
        "group.drive.settings"         => "#project_id#/groups/#group_id#/drive/settings/",

        "group.tasks"                => "#project_id#/groups/#group_id#/tasks/",
        "group.tasks.list"           => "#project_id#/groups/#group_id#/tasks/#task_id#/",
        "group.tasks.list.item.edit" => "#project_id#/groups/#group_id#/tasks/#task_id#/edit/",

        "group.users"                => "#project_id#/groups/#group_id#/users/"

    ];


    public static function getProjectListLink (){
        return '/workproject/'.str_replace([], [], self::linkPatherns['project.list']);
    }
    public static function getProjectItemLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.list.item']);
    }
    public static function getProjectItemEditLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.list.item.edit']);
    }
    public static function getProjectLockedLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.locked']);
    }
    public static function getProjectSettingsLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.list.settings']);
    }
    public static function getProjectDirectionListLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.direction.list']);
    }
    public static function getProjectDirectionItemLink ($projectId, $directionId){
        return '/workproject/'.str_replace(["#project_id#", "#direction_id#"], [$projectId, $directionId], self::linkPatherns['project.direction.item']);
    }
    public static function getProjectDirectionItemEditLink ($projectId, $directionId){
        return '/workproject/'.str_replace(["#project_id#", "#direction_id#"], [$projectId, $directionId], self::linkPatherns['project.direction.item.edit']);
    }
    public static function getProjectDiscussionListLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.discussion.list']);
    }
    public static function getProjectDiscussionItemLink ($projectId, $themeId){
        return '/workproject/'.str_replace(["#project_id#", "#theme_id#"], [$projectId, $themeId], self::linkPatherns['project.discussion.item']);
    }
    public static function getProjectDiscussionItemEditLink ($projectId, $themeId){
        return '/workproject/'.str_replace(["#project_id#", "#theme_id#"], [$projectId, $themeId], self::linkPatherns['project.discussion.item.edit']);
    }
    public static function getProjectDiscussionSettingsLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.discussion.settings']);
    }
    public static function getProjectCalendarLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.calendar']);
    }
    public static function getProjectCalendarItemLink ($projectId, $eventId){
        return '/workproject/'.str_replace(["#project_id#", "#event_id#"], [$projectId, $eventId], self::linkPatherns['project.calendar.item']);
    }
    public static function getProjectCalendarItemEditLink ($projectId, $eventId){
        return '/workproject/'.str_replace(["#project_id#", "#event_id#"], [$projectId, $eventId], self::linkPatherns['project.calendar.item.edit']);
    }
    public static function getProjectCalendarSettingsLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.calendar.settings']);
    }
    public static function getProjectAdListLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.ad.list']);
    }
    public static function getProjectAdItemLink ($projectId, $adId){
        return '/workproject/'.str_replace(["#project_id#", "#ad_id#"], [$projectId, $adId], self::linkPatherns['project.ad.item']);
    }
    public static function getProjectAdItemEditLink ($projectId, $adId){
        return '/workproject/'.str_replace(["#project_id#", "#ad_id#"], [$projectId, $adId], self::linkPatherns['project.ad.item.edit']);
    }
    public static function getProjectAdSettingsLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.ad.settings']);
    }
    public static function getProjectDriveLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.drive']);
    }
    public static function getProjectDrivePathLink ($projectId, $folderId){
        return '/workproject/'.str_replace(["#project_id#", "#folder_id#"], [$projectId, $folderId], self::linkPatherns['project.drive.path']);
    }
    public static function getProjectDriveSettingsLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.drive.settings']);
    }
    public static function getProjectUsersListLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.users.list']);
    }
    public static function getProjectUsersUpdateLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.users.update']);
    }
    public static function getProjectUserListLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.users']);
    }
    public static function getGroupListLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['group.list']);
    }
    public static function getGroupItemLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.list.item']);
    }
    public static function getGroupItemEditLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.list.item.edit']);
    }
    public static function getGroupLockedLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.locked']);
    }
    public static function getGroupSettingsLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.list.item.settings']);
    }
    public static function getGroupDiscussionListLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.discussion.list']);
    }
    public static function getGroupDiscussionItemLink ($projectId, $groupId, $themeId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#theme_id#"], [$projectId, $groupId, $themeId], self::linkPatherns['group.discussion.item']);
    }
    public static function getGroupDiscussionItemEditLink ($projectId, $groupId, $themeId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#theme_id#"], [$projectId, $groupId, $themeId], self::linkPatherns['group.discussion.item.edit']);
    }
    public static function getGroupDiscussionSettingsLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.discussion.settings']);
    }
    public static function getGroupCalendarLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.calendar']);
    }
    public static function getGroupCalendarItemLink ($projectId, $groupId, $eventId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#event_id#"], [$projectId, $groupId, $eventId], self::linkPatherns['group.calendar.item']);
    }
    public static function getGroupCalendarItemEditLink ($projectId, $groupId, $eventId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#event_id#"], [$projectId, $groupId, $eventId], self::linkPatherns['group.calendar.item.edit']);
    }
    public static function getGroupCalendarSettingsLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.calendar.settings']);
    }
    public static function getGroupAdListLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.ad.list']);
    }
    public static function getGroupAdItemLink ($projectId, $groupId, $adId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#ad_id#"], [$projectId, $groupId, $adId], self::linkPatherns['group.ad.item']);
    }
    public static function getGroupAdItemEditLink ($projectId, $groupId, $adId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#ad_id#"], [$projectId, $groupId, $adId], self::linkPatherns['group.ad.item.edit']);
    }
    public static function getGroupAdSettingsLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.ad.settings']);
    }
    public static function getGroupDriveLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.drive']);
    }
    public static function getGroupDrivePathLink ($projectId, $groupId, $folderId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#folder_id#"], [$projectId, $groupId, $folderId], self::linkPatherns['group.drive.path']);
    }
    public static function getGroupDriveSettingsLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.drive.settings']);
    }
    public static function getGroupUsersListLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.users.list']);
    }
    public static function getGroupUsersEditLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.users.edit']);
    }
    public static function getProjectTasksLink ($projectId){
        return '/workproject/'.str_replace(["#project_id#"], [$projectId], self::linkPatherns['project.tasks']);
    }
    public static function getProjectTasksListLink ($projectId, $taskId){
        return '/workproject/'.str_replace(["#project_id#", "#task_id#"], [$projectId, $taskId], self::linkPatherns['project.tasks.list']);
    }
    public static function getProjectTasksListItemEditLink ($projectId, $taskId){
        return '/workproject/'.str_replace(["#project_id#", "#task_id#"], [$projectId, $taskId], self::linkPatherns['project.tasks.list.item.edit']);
    }
    public static function getGroupTasksLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.tasks']);
    }
    public static function getGroupTasksListLink ($projectId, $groupId, $taskId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#task_id#"], [$projectId, $groupId, $taskId], self::linkPatherns['group.tasks.list']);
    }
    public static function getGroupTasksListItemEditLink ($projectId, $groupId, $taskId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#", "#task_id#"], [$projectId, $groupId, $taskId], self::linkPatherns['group.tasks.list.item.edit']);
    }
    public static function getGroupUserListLink ($projectId, $groupId){
        return '/workproject/'.str_replace(["#project_id#", "#group_id#"], [$projectId, $groupId], self::linkPatherns['group.users']);
    }

    /*
        Bitrix\Main\Loader::includeModule('gpi.workproject');

        $pathers = Gpi\Workproject\Entity\UrlManager::linkPatherns;

        foreach($pathers as $tag => $link){
            $functionName = 'get'.dashesToCamelCase($tag, '.').'Link';
            $words = explode('#', $link);
            foreach($words as $intager => $val){
                if($intager % 2 === 0){
                    unset($words[$intager]);
                }
            }
            $words2 = implode(', ',
                        array_map(function($v){
                            return '$'.lcfirst(dashesToCamelCase($v, '_'));
                        }, $words)
                    );
            $words = implode(', ',
                        array_map(function($v){
                            return '"#'.$v.'#"';
                        }, $words));
        dump(
        "public static function $functionName ($words2){
            return '/workproject/'.str_replace([$words], [$words2], self::linkPatherns['$tag']);
        }
        ");
        }


        function dashesToCamelCase($string, $replaceWord)
        {

            $str = str_replace(' ', '', ucwords(str_replace($replaceWord, ' ', $string)));
            return $str;
        }

     */

}