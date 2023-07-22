<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Loader,
    Gpi\Workproject\Orm;

class RSEntityStructure extends  \CBitrixComponent implements Controllerable{
    function configureActions(){}

    function getEditComponentResultAction(){
        global $APPLICATION;

        $APPLICATION->IncludeComponent(
            "rs:department",
            "edit",
            []
        );

        $APPLICATION->ShowHead();
    }

    function defineDepartmentInfo(){

        $this->arResult['DEPARTMENT'] = \Bitrix\Main\SiteTable::getList([
            'select' => [
                '*',
                //'D_' => 'DEPARTMENT.*'
            ],
            'filter' => ['LID' => SITE_ID],
            /*'runtime' => [
                'DEPARTMENT' => [
                    'data_type' => 'Gpi\Workproject\Orm\DepartmentTable',
                    'reference' => ['this.LID' => 'ref.SITE_ID'],
                ]
            ]*/
        ])->fetch();

    }

    function includeExtensions(){
        CJSCore::Init([
            'ui.buttons',
            'ui.buttons.icons',
            'sidepanel'
        ]);
    }

    public function executeComponent() {

        //if(!Loader::IncludeModule('gpi.workproject'))
        //    return;

        $template = $this->getTemplateName();
        switch ($template){

            case '.default':
                $this->defineDepartmentInfo();
                break;

            case 'preview':
                $this->defineDepartmentInfo();
                break;

            case 'edit':
                $this->defineDepartmentInfo();
                break;
        }
        

        $this->includeExtensions();
        $this->IncludeComponentTemplate();
    }
}