<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Application,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity;

class ForumDiscussion extends  \CBitrixComponent{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['FORUM_ID'])
            $params['FORUM_ID'] = 1;

        $params['USER_PERMISSIONS'] = Entity\EditorManager::defineForumPermission($params['FORUM_ID']);

        return $params;
    }

    public function executeComponent() {
        global $APPLICATION;

        $arDefaultUrlTemplates404 = array(
            "discussion.list" => "",
            "discussion.item" => "#discussion_id#/",
            "discussion.item.edit" => "#discussion_id#/edit/",
            "discussion.message.edit" => "#discussion_id#/edit/#message_id#/",
            "discussion.message.answer" => "#discussion_id#/edit/#message_id#/answer/",
            "settings" => "settings/",
            "locked" => "locked/",
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "discussion_id",
        );
        $arVariables = array();

        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

        $engine = new CComponentEngine($this);
        if (CModule::IncludeModule('iblock'))
        {
            $engine->addGreedyPart("#SECTION_CODE_PATH#");
            $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
        }

        $componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"],
            $arUrlTemplates,
            $arVariables
        );

        $b404 = false;
        if(!$componentPage)
        {
            $componentPage = "discussion.list";
            $b404 = true;
        }

        CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        $this->arResult = array_merge($this->arResult, array(
            "FOLDER" => $this->arParams["SEF_FOLDER"],
            "URL_TEMPLATES" => $arUrlTemplates,
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases,
        ));


        $this->arParams['COMPONENT_PAGE'] = $componentPage;

        $this->IncludeComponentTemplate($componentPage);
    }
}
