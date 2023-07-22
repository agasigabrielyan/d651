<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    \Bitrix\Main\Loader,
    Bitrix\Crm\Service\Container,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity,
    \Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable;



class PortalSupport extends  \CBitrixComponent implements Controllerable{

    public function configureActions(): array
    {
        return [
            'addRequest' => [
                'prefilters' => [
                ]
            ]
        ];
    }

    function addRequestAction($data){

        global $USER;

        $arFields = [
            'IBLOCK_ID' => 4,
            "CREATED_BY"    => $USER->GetID(),
        ];
        foreach ($data as $prop => $value){
            if(strpos($prop, 'UF_') === 0)
                $arFields['PROPERTY_VALUES'] = $value;
            else
                $arFields[$prop] = $value;
        }

        $el = new CIBlockElement;
        if($elementId = $el->Add($arFields))
            return json_encode([
                'status' => 1,
                'elemenId' => $elementId,
            ]);
        else
            return json_encode([
                'status' => 0,
                'error' => $el->LAST_ERROR,
            ]);
    }

    function defineUserInfo(){

        global $USER;

        if(!$USER->getId())
            return;

        $this->arResult['USER_DATA'] = \Bitrix\Main\UserTable::getList([
            'filter' => ['ID' => $USER->getId()],
            'select' => ['*', 'UF_*']
        ])->fetch();
    }

    function defineBCAddresses(){
        if(!Loader::includeModule("highloadblock"))
            return;

        $entity = HL\HighloadBlockTable::compileEntity(HL\HighloadBlockTable::getById(1)->fetch())->getDataClass();

        $this->arResult['BCS'] = $entity::getList(['order' => ['UF_NAME' => 'ASC']])->fetchAll();
    }

    public function executeComponent() {

        $this->defineUserInfo();

        $this->defineBCAddresses();

        $this->IncludeComponentTemplate();
    }

}
