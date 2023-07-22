<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init(array("jquery","sidepanel","fx", 'ajax'));
use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class ActivityDirectionsSettings extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineActivityDirectionsPermission();


        return $params;
    }

    public function configureActions(){}

    public static function syncDirectionAdsPermissionsAction(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        $permissions = Orm\ActivityDirectionPermissionTable::getList()->fetchAll();

        $directions = Orm\DetailDirectionTable::getList([
            'select' => ['*']
        ])->fetchAll();

        $OldPermissionsRS = Orm\AdUnionUserPermissionTable::getList([
            'select' => ['*'],
            'filter' => [
                'UNION_ID' => array_column($directions, 'AD_UNION_ID')
            ]
        ]);
        while($oldPermission = $OldPermissionsRS->fetch())
            Orm\AdUnionUserPermissionTable::delete($oldPermission['ID']);

        foreach ($directions as $direction){
            foreach ($permissions as $permission){
                $permission['UNION_ID'] = $direction['AD_UNION_ID'];
                Orm\AdUnionUserPermissionTable::add(array_diff($permission, ['ID' => $permission['ID']]));
            }
        }


    }

    public function executeComponent() {
        if(!in_array('X', $this->arParams['USER_PERMISSIONS'])){
            header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
            return;
        }


        global $APPLICATION;
        $APPLICATION->setTitle('Настройка направлений деятельности');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));

        $this->includeComponentTemplate();
    }
}
