<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\UserTable;

$request = Application::getInstance()->getContext()->getRequest();
if(!$request->isAjaxRequest())
    die('request prevented. Ajax required.');

$action = $request->get("action");
$props = $request->get("props");
$filter = $request->get("filter");

switch ($action){
    case 'getUsers':
        echo getUsers($props, $filter);
        break;
}


function getUsers($props, $preFilter){
    $filter=[
        '!label' => '  '
    ];

    if($preFilter)
        $filter = array_merge($filter, $preFilter);

    unset($filter['UF_UPPER_DEPARTMENT']);

    if($props['name'])
        $filter['label'] = '%'.$props['name'].'%';

    $usersList = UserTable::getList([
        'filter' => $filter,
        'select' => ['key' => 'ID', 'label', 'EMAIL', 'UF_DEPARTMENT',],
        'limit' => 20,
        'runtime' => [
            new \Bitrix\Main\Entity\ExpressionField(
                'label',
                'CONCAT(%s, " ", %s, " ", %s)',
                ['LAST_NAME', 'NAME', 'SECOND_NAME']
            )
        ]
    ])->fetchAll();

    $sectionsRS = Bitrix\Iblock\SectionTable::getList([
        'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
        'filter' => ['!ID' => 1],
        'order' => ['DEPTH_LEVEL' => 'asc'],
    ]);
    while($section = $sectionsRS->fetch()){
        $depsMap[$section['ID']]= [
            'NAME' => $section['NAME'],
            'PARENT' => $section['IBLOCK_SECTION_ID'],
        ];
    }

    $res = array_map(function($v) use ($depsMap){
            $v['id'] = $v['key'];
            $upperDep = current($v['UF_DEPARTMENT']);
            while( $depsMap[$upperDep] && $depsMap[$upperDep]['PARENT'] != 1 ){
                $upperDep = $depsMap[$upperDep]['PARENT'];
            }
            $v['upper_dep_id'] =  $upperDep;
            $v['upper_dep_name'] =  $depsMap[$upperDep]['NAME'];
            return $v;
        },$usersList);

    $dep = $preFilter['UF_UPPER_DEPARTMENT'];

    if(intval($dep)>0){
        $res = array_filter($res, function ($user) use($dep){

            if(is_array($dep) && $dep[$user['upper_dep_id']]){
                return true;
            }else if(intval($user['upper_dep_id']) == intval($dep))
               return true;


        });
    }


    return json_encode([ 'list' => $res]);
}



?>