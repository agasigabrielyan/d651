<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;



class RSActivityDirections extends  \CBitrixComponent implements Controllerable{
    public function configureActions(){}

    function defineComponentPage(){

        global $APPLICATION;
        
        $arDefaultUrlTemplates404 = array(
            'main_directions_map' =>                            '',
            'content' =>                                        'content/',
            'docs' =>                                           'docs/',
            'settings' =>                                       'settings/',
            "main_directions_item_edit" =>                      "#main_id#/edit/",
            "detail_direction_item" =>                          "#main_id#/directions/#direction_id#/",
            "detail_direction_item_edit" =>                     "#main_id#/directions/#direction_id#/edit/",
            "detail_direction_item_dicuments" =>                "#main_id#/directions/#direction_id#/documents/",
            "detail_direction_item_dicuments_item_edit" =>      "#main_id#/directions/#direction_id#/documents/#document_id#/edit/",
            "detail_direction_item_order" =>                    "#main_id#/directions/#direction_id#/order/",
            "detail_direction_item_order_item_edit" =>          "#main_id#/directions/#direction_id#/order/#order_id#/edit/",
            "detail_direction_item_events" =>                   "#main_id#/directions/#direction_id#/events/",
            "detail_direction_item_events_item" =>              "#main_id#/directions/#direction_id#/events/#event_id#/",
            "detail_direction_item_events_item_edit" =>         "#main_id#/directions/#direction_id#/events/#event_id#/edit/",
            "detail_direction_item_ads" =>                      "#main_id#/directions/#direction_id#/ads/",
            "detail_direction_item_ads_item" =>                 "#main_id#/directions/#direction_id#/ads/#ad_id#/",
            "detail_direction_item_ads_item_edit" =>            "#main_id#/directions/#direction_id#/ads/#ad_id#/edit/",
            "detail_direction_item_ads_settings" =>             "#main_id#/directions/#direction_id#/ads/settings/",
            "detail_direction_item_important" =>                "#main_id#/directions/#direction_id#/importants/",
            "detail_direction_item_important_item" =>           "#main_id#/directions/#direction_id#/importants/#important_id#/",
            "detail_direction_item_important_item_edit" =>      "#main_id#/directions/#direction_id#/importants/#important_id#/edit/",
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "main_id",
            "direction_id",
            "document_id",
            "order_id",
            "event_id",
            "ad_id",
            "important_id",
        );

        if($this->arParams["SEF_MODE"] == "Y")
        {
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
                $componentPage = "main_directions_map";
                $b404 = true;
            }

            CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

            $this->arResult = array(
                "FOLDER" => $this->arParams["SEF_FOLDER"],
                "URL_TEMPLATES" => $arUrlTemplates,
                "VARIABLES" => $arVariables,
                "ALIASES" => $arVariableAliases,
            );
        }
        else
        {
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $this->arParams["VARIABLE_ALIASES"]);
            CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

            $componentPage = "main_directions_map";

            $this->arResult = array(
                "FOLDER" => "",
                "URL_TEMPLATES" => array(
                    "directions" => htmlspecialcharsbx($APPLICATION->GetCurPage()),
                ),
                "VARIABLES" => $arVariables,
                "ALIASES" => $arVariableAliases
            );
        }


        if(!\Bitrix\Main\Loader::IncludeModule('gpi.workproject'))
            return;

        if($arVariables['direction_id'])
            $APPLICATION->addHeadString('<script>window.new_element_id = '.$arVariables['direction_id'].';</script>');


        if($arVariables['main_id'])
            $this->arParams['ACTIVITY_DATA'] = Gpi\Workproject\Orm\ActivityDirectionTable::getById($arVariables['main_id'])->fetch();

        if($arVariables['direction_id']){
            $this->arParams['DIRECTION_DATA'] = Gpi\Workproject\Orm\DetailDirectionTable::getById($arVariables['direction_id'])->fetch();
            $this->arParams['DIRECTION_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#','#direction_id#'], [$arVariables['main_id'], $arVariables['direction_id']], $this->arResult['URL_TEMPLATES']['detail_direction_item']);
        }

        $this->arParams['COMPONENT_PAGE'] = $componentPage;

        $this->arParams['USER_PERMISSIONS'] = Gpi\Workproject\Entity\EditorManager::defineActivityDirectionsPermission();
        return $componentPage;
    }

    public function executeComponent() {

        $componentPage = $this->defineComponentPage();

        $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineActivityDirectionsPermission();

        $this->includeComponentTemplate($componentPage);
    }

}
