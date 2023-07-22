<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager;


class RsNotes extends  \CBitrixComponent{

    public function executeComponent() {

        global $APPLICATION;

        $arDefaultUrlTemplates404 = array(
            "list" => "",
            "item" => "#note_id#/",
            "item.edit" => "#note_id#/edit/",
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "task_id",
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

        $this->IncludeComponentTemplate($componentPage);
    }

}
