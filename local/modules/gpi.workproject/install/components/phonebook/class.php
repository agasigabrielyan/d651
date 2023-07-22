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



class RSPhoneBook extends  \CBitrixComponent implements Controllerable{

    public function configureActions(): array
    {
        return [
            'addRequest' => [
                'prefilters' => [
                ]
            ]
        ];
    }

    function getLikeContectsAction($fio, $count){

        return file_get_contents("https://www1.adm.gazprom.ru/Phones_pre/PhoneWebService.asmx/GetEmployeesByFIO?fio=$fio&count=$count");
    }

    public function executeComponent() {

        $this->IncludeComponentTemplate();
    }

}
