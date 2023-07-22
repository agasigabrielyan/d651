<?php

use \Bitrix\Main\Loader,
    \Bitrix\Main\Application,
    \Bitrix\Main\Engine\Contract\Controllerable,
    lib\FavservicesTable,
    \Bitrix\Iblock\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class IndustryServices extends \CBitrixComponent  implements Controllerable
{
    private $_request;
    public $arResult = [];

    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function _checkModules() {
        if ( !Loader::includeModule('iblock') ) {
            throw new \Exception('Не загружен модуль инфоблоков необходимый для работы компонента');
        }
        return true;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllMain|CMain
     */
    private function _app() {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllUser|CUser
     */
    private function _user() {
        global $USER;
        return $USER;
    }

    public function configureActions()
    {
        return [
            'getFavorites' => [
                'prefilters' => []
            ],
            'addDelFavorite' => [
                'prefilters' => []
            ]
        ];
    }

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
        //$arParams["IBLOCK_API_CODE"] = trim($arParams["IBLOCK_API_CODE"]);


        return $arParams;
    }

    private function getServices()
    {

        $services = Iblock::wakeUp($this->arParams["IBLOCK_ID"])->getEntityDataClass()::getList([
            'select' => ['ID', 'NAME', 'PREVIEW_TEXT', 'LINK_' => 'LINK'],
        ])->fetchAll();

        return $services;
    }

    public function getFavoritesAction()
    {

        $arItems = [];

        $rsItems = FavservicesTable::getList([
            'select' => ["*"],
            'filter' => ['UF_UID' => $this->_user()->GetId()],
            'order' => [],
            'cache' => ["ttl" => 0, 'cache_joins' => true]
        ]);

        while ($hlElement = $rsItems->fetch()){
            $arItems[] =
                [
                    'ID' => $hlElement['ID'],
                    'UF_UID' => $hlElement['UF_UID'],
                    'UF_ELID' => $hlElement['UF_ELID'],
                    'URL' => $hlElement['UF_URL'],
                    'NAME' => $hlElement['UF_NAME']
                ];
        }

        return $arItems;

    }

    public function addDelFavoriteAction($flag, array $params = [])
    {

        if($flag == 'add'){

            $result = FavservicesTable::add(
                [
                    'UF_UID' => $params['uid'],
                    'UF_ELID' => $params['elid'],
                    'UF_URL' => $params['url'],
                    'UF_NAME' => $params['name']
                ]
            );

            if ($result->isSuccess())
            {
                $id = $result->getId();
                return ['ID: '. $id . ' ADDED!'];
            }

        }else if($flag == 'del'){

            $res = FavservicesTable::getList([
                    'select' => ['ID'],
                    'filter' => ['UF_ELID' => $params['elid'], 'UF_UID' => $params['uid']]
                ]
            );

            $id = $res->fetch();

            if($id){
                FavservicesTable::delete($id);
                return [" DELETED!"];
            }

        }else{

            return ["NO CONDITIONS!"];

        }

    }

    public function executeComponent()
    {
        $this->_checkModules();
        $this->_app();
        $this->_user();

        $this->_request = Application::getInstance()->getContext()->getRequest();

        $this->arResult["SERVICES"] = self::getServices();
        $this->arResult["FAV_SERVICES"] = self::getFavoritesAction();

        // is favorite to current user?
        foreach ($this->arResult["FAV_SERVICES"] as $fav){
            foreach ($this->arResult["SERVICES"] as $key => $val){
                if($fav["UF_ELID"] == $val["ID"]){
                    $this->arResult["SERVICES"][$key]["IS_FAVORITE"] = 1;
                }
            }
        }

        $this->IncludeComponentTemplate();
    }
}