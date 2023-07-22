<?php   require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Application,
    \Bitrix\Main\Context,
    \Bitrix\Main\Loader,
    \Bitrix\Main\Request,
    \Gpi\Workproject\Entity;



$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$requestParams = $request->getPostList();


if(!$request->isAjaxRequest())
    die('bad request');


switch($request['action']){

    case 'WRITED_REED_ACTION':
        Entity\Updates\Writer::unsetUserNews($request['newId']);
        break;
}