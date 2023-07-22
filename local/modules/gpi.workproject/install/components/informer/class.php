<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    \Bitrix\Main\Loader,
    Bitrix\Crm\Service\Container,
    \Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable;



class Informer extends  \CBitrixComponent implements Controllerable{

    public function configureActions(): array
    {
        return [];
    }


    public static function setCompactBlockContentAction($content){
        global $USER;
        global $APPLICATION;

        session_start();
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('UF_COMPACT_BLOCK_CONTENT', $content);

        $user = new CUser;
        $user->Update($USER->getId(), ["UF_COMPACT_BLOCK_CONTENT" => $content]);

        ob_start();
        switch ($content){
            case 'NOTES' :
                self::getNotesContent();
                break;

        }
        ?><head><?

        $Asset = Bitrix\Main\Page\Asset::getInstance();
        echo $Asset->getJs();
        echo $Asset->getCss();
        ?></head><?
        return ob_get_clean();
    }

    public static function getNotesContent(){
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            "rs:notes",
            "",
            [
                'SEF_FOLDER' => '/notes/'
            ],
            null,
            array("HIDE_ICONS" => "Y")
        );

    }

    function defineComponentPage(){

        global $APPLICATION;

        $arDefaultUrlTemplates404 = array(
            'informer' =>                             '',
            'notes_item' =>                           'notes/#note_id#/',
            "notes_item_edit" =>                      "notes/#note_id#/edit/",
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "note_id",
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
            $componentPage = "informer";
            $b404 = true;
        }

        CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        $this->arResult['FOLDER'] = $this->arParams["SEF_FOLDER"];
        $this->arResult['URL_TEMPLATES'] = $arUrlTemplates;
        $this->arResult['VARIABLES'] = $arVariables;
        $this->arResult['ALIASES'] = $arVariableAliases;

        $this->arParams['COMPONENT_PAGE'] = $componentPage;

        return $componentPage;
    }

    function defineActiveTab(){
        global $USER;

        $this->arResult['TARGET'] = Bitrix\Main\UserTable::getList([
            'filter' => [
                'ID' => $USER->getId()
            ],
            'select' => ['UF_COMPACT_BLOCK_CONTENT']
        ])->fetch()['UF_COMPACT_BLOCK_CONTENT'];
    }

    public function executeComponent()
    {
        $this->defineActiveTab();

        $page = $this->defineComponentPage();
        $this->IncludeComponentTemplate($page);
    }

}
