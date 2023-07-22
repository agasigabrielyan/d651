<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Entity\UrlManager;



class Workprojects extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);

        return $params;
    }

    public function configureActions(){}

    function ensureDirectorsClearMind(){
        global $APPLICATION;
        $APPLICATION->AddHeadString("
            <style>.ui-btn-icon-setting{display:none;}</style>
        ");
    }

    public function executeComponent() {

        if(!Bitrix\Main\Loader::IncludeModule('gpi.workproject'))
            return;

        global $APPLICATION;
        global $USER;

        /** @var CBitrixComponent $this */
        /** @var array $this->>arParams */
        /** @var array $this->>arResult */
        /** @var string $componentPath */
        /** @var string $componentName */
        /** @var string $componentTemplate */
        /** @global CDatabase $DB */
        /** @global CUser $USER */
        /** @global CMain $APPLICATION */

        $arDefaultUrlTemplates404 = UrlManager::linkPatherns;

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(

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
            $componentPage = "project.list";
            $b404 = true;
        }

        CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        $this->arResult = array(
            "FOLDER" => $this->arParams["SEF_FOLDER"],
            "URL_TEMPLATES" => $arUrlTemplates,
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases,
        );


        $this->arParams['COMPONENT_PAGE'] = $componentPage;


        if($this->arResult['VARIABLES']['project_id']){
            $this->arResult['PROJECT'] = Orm\ProjectTable::getById($this->arResult['VARIABLES']['project_id'])->fetch();
            $this->arResult['PROJECT_PATH'] = UrlManager::getProjectItemLink($this->arResult['VARIABLES']['project_id']);
            $APPLICATION->AddChainItem('Проект: '.$this->arResult['PROJECT']['TITLE'], UrlManager::getProjectItemLink($this->arResult['PROJECT']['ID']));


            if(!$this->arParams['CURRENT_USER_CATEGORIES'] && $USER->IsAuthorized())
                $this->arParams['CURRENT_USER_CATEGORIES'] = Orm\ProjectUserTable::getList([
                    'filter' => [
                        'USER_ID' => $USER->getId(),
                        'PROJECT_ID' => $this->arResult['VARIABLES']['project_id']
                    ]
                ])->fetch()['CATEGORY'];

            if(!array_intersect([6], $this->arParams['CURRENT_USER_CATEGORIES']))
                $this->ensureDirectorsClearMind();

        }if($this->arResult['VARIABLES']['group_id']) {
            $this->arResult['GROUP'] = Orm\GroupItemTable::getById($this->arResult['VARIABLES']['group_id'])->fetch();

            $APPLICATION->AddChainItem('Список групп', UrlManager::getGroupListLink($this->arResult['PROJECT']['ID']));
            $APPLICATION->AddChainItem('Группа: '.$this->arResult['GROUP']['TITLE'], UrlManager::getGroupItemLink($this->arResult['PROJECT']['ID'], $this->arResult['GROUP']['ID']));
        }

        if($this->arResult['VARIABLES']['group_id']){
            $this->arResilt['MENU'] = [
                [
                    'TEXT' => 'Основное',
                    'LINK' => UrlManager::getGroupItemLink($this->arResult['VARIABLES']['project_id'], $this->arResult['VARIABLES']['group_id']),
                ],
                [
                    'TEXT' => 'Календарь',
                    'LINK' => UrlManager::getGroupCalendarLink($this->arResult['VARIABLES']['project_id'], $this->arResult['VARIABLES']['group_id']),
                ],
                [
                    'TEXT' => 'Задачи',
                    'LINK' => UrlManager::getGroupTasksLink($this->arResult['VARIABLES']['project_id'], $this->arResult['VARIABLES']['group_id']),
                    //'PERMISSION' => 'D',
                ],
                [
                    'TEXT' => 'Диск',
                    'LINK' => UrlManager::getGroupDriveLink($this->arResult['VARIABLES']['project_id'], $this->arResult['VARIABLES']['group_id']),
                ],
                [
                    'TEXT' => 'Объявления',
                    'LINK' => UrlManager::getGroupAdListLink($this->arResult['VARIABLES']['project_id'], $this->arResult['VARIABLES']['group_id']),
                ],
                [
                    'TEXT' => 'Участники',
                    'LINK' => UrlManager::getGroupUserListLink($this->arResult['VARIABLES']['project_id'], $this->arResult['VARIABLES']['group_id']),
                ],
            ];
        }else if($this->arResult['VARIABLES']['project_id']){
            $this->arResilt['MENU'] = [
                [
                    'TEXT' => 'Основное',
                    'LINK' => $this->arResult['PROJECT_PATH'],
                ],
                [
                    'TEXT' => 'Группы',
                    'LINK' => UrlManager::getGroupListLink($this->arResult['VARIABLES']['project_id']),
                ],
                [
                    'TEXT' => 'Календарь',
                    'LINK' => UrlManager::getProjectCalendarLink($this->arResult['VARIABLES']['project_id']),
                ],
                [
                    'TEXT' => 'Задачи',
                    'LINK' => UrlManager::getProjectTasksLink($this->arResult['VARIABLES']['project_id']),
                    //'PERMISSION' => 'D',
                ],
                [
                    'TEXT' => 'Диск',
                    'LINK' => UrlManager::getProjectDriveLink($this->arResult['VARIABLES']['project_id']),
                ],
                [
                    'TEXT' => 'Объявления',
                    'LINK' => UrlManager::getProjectAdListLink($this->arResult['VARIABLES']['project_id']),
                ],
            ];
        }

        $context = Bitrix\Main\Application::getInstance()->getContext();
        $server = $context->getServer();

        $correctUrl = substr($server['REQUEST_URI'], 0, strpos($server['REQUEST_URI'],'?')?? strlen($server['REQUEST_URI']));
        if(!$correctUrl)
            $correctUrl = $server['REQUEST_URI'];

        global $BX_MENU_CUSTOM;
        foreach ($this->arResilt['MENU'] as $link){
            $correctLink = substr($link['LINK'], 0, strpos($link['LINK'],'?') ?? strlen($link['LINK']));
            if(!$correctLink)
                $correctLink = $link['LINK'];
            $BX_MENU_CUSTOM->AddItem('top', array_merge($link, [
                    'SELECTED' =>  $correctLink == $correctUrl,
                ])
            );
        }


        $this->arParams['PROJECT_GROUP_EXISTS'] = true;
        $this->arParams['PROJECT_GROUP_EXISTS_GROUUP_ID'] = $this->arResult['PROJECT']['GROUP_UNION_ID'];

        CJSCore::init(['rs.buttons', 'ui.dialogs.messagebox']);

        $this->includeComponentTemplate($componentPage);
    }

}
