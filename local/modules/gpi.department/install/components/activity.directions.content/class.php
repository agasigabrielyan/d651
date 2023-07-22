<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    \Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable;



class RSActivityDirectionsContent extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    function defineDirection(){
        $directionsRs = Orm\DetailDirectionTable::getList([
            'select' => ['MAIN_' => 'MAIN_DIRECTION', '*'],
            'order' => ['ID' => 'desc'],
            'runtime' => [
                'MAIN_DIRECTION' => [
                    'data_type' => '\Gpi\Workproject\Orm\ActivityDirectionTable',
                    'reference' => ['this.ACTIVITY_DIRECTION_ID' => 'ref.ID'],
                ],
            ]
        ]);

        while($direction = $directionsRs->fetch()){
            $this->arResult['DIRECTION_ADS'][] = $direction['AD_UNION_ID'];
            $this->arResult['DIRECTION_IDS'][] = $direction['ID'];
            $this->arResult['DIRECTIONS'][] = $direction;
        }

        $this->arParams['VARIABLES']['direction_id'] = $this->arResult['DIRECTION_IDS'];

    }


    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineDirection();

        $this->arResult['GRID_ID'] = 'rs_activity_directions_content';


        $this->IncludeComponentTemplate();
    }

}
