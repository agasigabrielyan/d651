<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity\UrlManager,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper,
    Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable;


class WorkprojectsItem extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:project.list.item",
            "",
            $params
        );

        return ob_get_clean();
    }

    public static function renameProjectAction($id, $title, $director){
        Orm\ProjectTable::update($id, ['TITLE' => $title, 'DIRECTOR_ID' => $director]);
    }

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);

        return $params;
    }


    function defineProjectData(){
        $groupTable = new Orm\ProjectTable();

        $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['project.list.item'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['project.list.item.edit'];

        $this->arResult['PROJECT'] = $groupTable::getList([
            'select' => ['*', 'CREATOR_FIO', 'LINK', 'EDIT_LINK'],
            'filter' => ['ID' => $this->arParams['VARIABLES']['project_id']],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE("'.$viewPath.'", "#project_id#", %s)',
                    ['ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE("'.$editPath.'", "#project_id#", %s)',
                    ['ID']
                ),
            ]
        ])->fetch();

        $rightsTable = new Orm\ProjectUserTable();

        foreach ($rightsTable->getMap() as $field){
            $fields[$field->getColumnName()] = $field->getParameter('value');
        }

        foreach ($fields['CATEGORY'] as $categoryTitle){
            $this->arResult['USERS_CATEGORIES'][] = [
                'id' => $categoryTitle,
                'label' => $categoryTitle
            ];
        }
    }

    function defineParams(){
        global $APPLICATION;

        CJSCore::init(["sidepanel", 'rs.buttons', 'bootsrap5', 'ui.entity-selector', 'cool.editor']);

        $APPLICATION->setTitle('Проект: '.$this->arResult['PROJECT']['NAME']);

        $this->arResult['GRID_ID'] = 'project_item';
        $this->arResult['GROUP_PREVIEW'] = '/media/projects-dark.jpg';
        $this->arResult['PREVIEW'] = '/media/project-img.jpg';
        $this->arResult['PRJECT_PATH'] = UrlManager::getProjectItemLink($this->arResult['PROJECT']['ID']);
        $this->arResult['DRIVE_PATH'] = UrlManager::getProjectDriveLink($this->arResult['PROJECT']['ID']);
        $this->arResult['ADS_PATH'] = UrlManager::getProjectAdListLink($this->arResult['PROJECT']['ID']);
        $this->arResult['DISCUSSION_PATH'] = UrlManager::getProjectDiscussionListLink($this->arResult['PROJECT']['ID']);
        $this->arResult['SETTINGS_PATH'] = "{$this->arParams['PRJECT_PATH']}settings/";
    }

    public function definePermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($this->arParams['VARIABLES']['project_id']);

        if($this->arParams['VARIABLES']['project_id'] == 0)
            return true;

        if($this->arParams['COMPONENT_PAGE'] == 'project.list.item.edit'
            &&
            !(
                array_intersect(['X'], $this->arParams['USER_PERMISSIONS'])
                ||
                (array_intersect(['W'], $this->arParams['USER_PERMISSIONS']) && $this->arParams['CREATED_BY'] == $USER->getId())
            )
        )
            header('Location: '.Entity\UrlManager::getProjectLockedLink($this->arParams['VARIABLES']['project_id']));

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']))
            return true;

        header('Location: '.Entity\UrlManager::getProjectLockedLink($this->arParams['VARIABLES']['project_id']));
    }
    
    public function executeComponent() {

        $this->definePermission();
        $this->defineProjectData();
        $this->defineParams();

        $this->IncludeComponentTemplate();
    }

}
