<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper,
    Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable;


class AdUnionListItem extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){
        return [
            'deleteNote' => [
                'prefilters' => [
                ]
            ]
        ];
    }

    public static function loadEntityAction(){
        if (!Loader::includeModule("gpi.workproject"))
            return;

        return Helper\FormData::save(new Orm\AdItemTable(), [], 1);
    }
    function deleteRequestAction($id){

        if(!Loader::includeModule("gpi.workproject"))
            return;

        $projectAdsTable = new Orm\AdItemTable();

        $addResult = $projectAdsTable::delete($id);



        if($addResult->isSuccess()){

            return json_encode([
                'status' => 1,
            ]);
        }else{
            return json_encode([
                'status' => 0,
                'error' => 'Ошибка: ' . implode(', ', $addResult->getErrors()),
            ]);
        }
    }
    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:ad.union.list.item",
            "",
            $params
        );

        return ob_get_clean();
    }

    function defineAd(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        $linkPathernEdit = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['item.edit'];
        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['item'];

        if($this->arParams['VARIABLES']['ad_id']){

            $projectAdsTable = new Orm\AdItemTable();

            $this->arResult['AD'] = $projectAdsTable::getlist([
                'select' => ['*', 'EDIT_LINK', 'LINK'],
                'filter' => [
                    'ID' => $this->arParams['VARIABLES']['ad_id']
                ],
                'runtime' => [
                    new \Bitrix\Main\Entity\ExpressionField(
                        'EDIT_LINK',
                        'REPLACE("'.$linkPathernEdit.'", "#ad_id#", %s)',
                        ['ID']
                    ),
                    new \Bitrix\Main\Entity\ExpressionField(
                        'LINK',
                        'REPLACE("'.$linkPathern.'", "#ad_id#", %s)',
                        ['ID']
                    ),
                ]
            ])->fetch();

            if(!$this->arResult['AD'])
                header('Location: '.$this->arParams['SEF_FOLDER']);

            if($this->arResult['AD']['PREVIEW'])
                $this->arResult['AD']['FILES'][] = $this->arResult['AD']['PREVIEW'];

            if(count($this->arResult['AD']['FILES']) > 0){
                $filesListRS = Bitrix\Main\FileTable::getList(['filter' => ['ID' => $this->arResult['AD']['FILES']]]);

                $this->arResult['AD']['FILES']=[];

                while($file = $filesListRS->fetch()){
                    $file['SRC'] = '/upload/'.$file['SUBDIR'].'/'.$file['FILE_NAME'];
                    if($this->arResult['AD']['PREVIEW'] != $file['ID'])
                        $this->arResult['AD']['FILES'][$file['ID']] = $file;
                    else
                        $this->arResult['AD']['PREVIEW'] = $file;
                }
            }
        }
    }

    public function definePermission(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($this->arParams['UNION_ID']);

        global $USER;

        if(
            $this->arResult['event']['CREATED_BY'] == $USER->getId() && array_intersect(['R'], $this->arParams['USER_PERMISSIONS'])
            ||
            array_intersect(['X'], $this->arParams['USER_PERMISSIONS'])
            ||
            !$this->arResult['event']['ID'] && array_intersect(['R'], $this->arParams['USER_PERMISSIONS'])
        )
            return true;


        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
        return false;
    }

    public function executeComponent() {

        $this->definePermission();

        $this->defineAd();

        $this->arResult['GRID_ID'] = 'workproject_ad';

        global $APPLICATION;

        $context = Bitrix\Main\Application::getInstance()->getContext();
        $server = $context->getServer();

        $APPLICATION->AddChainItem('Объявления', $this->arParams['SEF_FOLDER']);
        $APPLICATION->AddChainItem($this->arResult['AD']['TITLE'], $server['REQUEST_URI']);

        CJSCore::init(['sidepanel', 'ui.dialogs.messagebox', 'rs.buttons', 'cool.editor']);

        $this->IncludeComponentTemplate();
    }

}
