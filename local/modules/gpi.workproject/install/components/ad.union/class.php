<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Application,
    Bitrix\Main\loader,
    Gpi\Workproject\Entity;

class ADUnion extends  \CBitrixComponent{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['UNION_ID'])
            $params['UNION_ID'] = 1;

        $params['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($params['UNION_ID']);

        return $params;
    }

    public function executeComponent() {
        global $APPLICATION;

        $arDefaultUrlTemplates404 = array(
            "list" => "",
            "item" => "#ad_id#/",
            "item.edit" => "#ad_id#/edit/",
            "settings" => "settings/",
            "locked" => "locked/",
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "ad_id",
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
            $componentPage = "list";
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

        CJSCore::init(['sidepanel', 'ui.dialogs.messagebox', 'rs.buttons']);

        $this->IncludeComponentTemplate($componentPage);
    }
}
