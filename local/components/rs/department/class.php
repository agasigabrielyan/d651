<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Loader;

class RSEntityStructure extends  \CBitrixComponent implements Controllerable{
    function configureActions(){}

    function getEditComponentResultAction(){
        global $APPLICATION;

        $APPLICATION->IncludeComponent(
            "rs:department",
            "edit",
            []
        );

        $content = ob_get_clean();

        $styles = $APPLICATION->sPath2css;

        $strings = '';
        foreach ($styles as $style)
            $strings.="<link type='text/css' rel='stylesheet' href='$style'>";

        $strings=$strings.$APPLICATION->GetHeadScripts();

        return [
            'strings' => $strings,
            'content' => $content,
        ];
    }

    function defineDepartmentInfo(){


        $this->arResult['DEPARTMENT'] = \Bitrix\Main\SiteTable::getList([
            'select' => [
                '*',
                'D_' => 'DEPARTMENT.*',
            ],
            'filter' => ['LID' => SITE_ID],
            'runtime' => [
                'DEPARTMENT' => [
                    'data_type' => "\Bitrix\Iblock\SectionTable",
                    'reference' => ['this.LID' => 'ref.XML_ID'],
                ]
            ]
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

        if(!Loader::IncludeModule('iblock'))
            return;

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
