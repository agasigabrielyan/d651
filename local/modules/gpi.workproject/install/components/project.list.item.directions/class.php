<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity\UrlManager,
    Gpi\Workproject\Entity\EditorManager,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper,
    \Bitrix\Main\Loader,
    \Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable;


class WorkprojectsProjectDirections extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);

        return $params;
    }

    public function configureActions(){
        return [

        ];
    }

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:project.list.item.directions",
            "",
            $params
        );

        return ob_get_clean();
    }

    function getDirections(){
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['project.direction.item'];

        $projectActDirectionsTable = new Orm\ProjectDirectionTable();

        $this->arResult['ACTIVITY_DIRECTIONS'] = $projectActDirectionsTable::getList([
            'select' => [
                'FIO',
                'EDIT_LINK',
                '*'
            ],
            'filter' => [
                'PROJECT_ID' => $this->arParams['VARIABLES']['project_id'],
                '!ID' => $this->arParams['PROJECT']['PUBLIC_DIRECTION_ID']
            ],
            'runtime' => [
                'creator' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['creator.LAST_NAME','creator.NAME', 'creator.SECOND_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK_RS',
                    'REPLACE("'.$editPath.'", "#project_id#", %s)',
                    ['PROJECT_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#direction_id#", %s)',
                    ['EDIT_LINK_RS', 'ID']
                ),
            ]
        ])->fetchAll();
    }

    function checkUpdates(){

        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                'ENTITY_TYPE' => 'ProjectDirection',
                'ENTITY_ID' => array_column($this->arResult['ACTIVITY_DIRECTIONS'], 'ID'),
            ]
        ]);
        while($update = $updatesRS->fetch()){
            $key = array_search($update["ID"], array_column($this->arResult['ACTIVITY_DIRECTIONS'], 'ID'));
            $this->arResult['ACTIVITY_DIRECTIONS'][$key]['IS_NEW'] = true;
            $this->arResult['ACTIVITY_DIRECTIONS'][$key]['NEW_ID'] = $update['ID'];
        }
    }


    function defineParams(){
        $this->arResult['GRID_ID'] = 'project_directions';
        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#direction_id#', '#project_id#'], [0, $this->arParams['VARIABLES']['project_id']], $this->arParams['URL_TEMPLATES']['project.direction.item']);
    }



    public function executeComponent() {
        $this->getDirections();
        $this->defineParams();
        $this->checkUpdates();
        $this->IncludeComponentTemplate();
    }

}
