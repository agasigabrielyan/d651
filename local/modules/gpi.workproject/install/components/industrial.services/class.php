<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\loader,
    Bitrix\Main\UserTable;

class IndustrialSerices extends  \CBitrixComponent implements Controllerable{

	public function configureActions()
    {
        return [
            'unFavorIt' => [
                'prefilters' => [],
            ],
			'favorIt' => [
                'prefilters' => [],
            ],
        ];
    }

	public static function unFavorItAction($id)
    {
        global $USER;

        if(!$USER->getId()){
            ShowMessage('Необходимо авторизироваться');
            return;
        }

        $favoriteIds = json_decode(UserTable::getList([
            'filter' => ['ID' => $USER->getId()],
            'select' => ['UF_FAVORIT_INDUSTRIAL_SERVICES'],
        ])->fetch()['UF_FAVORIT_INDUSTRIAL_SERVICES'], true);

        $key = array_search($id, $favoriteIds, true);
        if ($key === false) {
            return json_encode([
                'status' => 0,
                'error' => 'Произошла ошибка'
            ]);
        }

        unset($favoriteIds[$key]);

        $USER->Update($USER->getId(), ['UF_FAVORIT_INDUSTRIAL_SERVICES' => json_encode(array_unique($favoriteIds))]);

        ob_start();

        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            "rs:industrial.services",
            "",
            [],
            null,
            array("HIDE_ICONS" => "Y")
        );

        $html = ob_get_clean();

        return json_encode([
            'status' => 1,
            'html' => $html
        ]);
    }

	public static function favorItAction($id){
        global $USER;

        if(!$USER->getId()){
            ShowMessage('Необходимо авторизироваться');
            return;
        }

        $favoriteIds = json_decode(UserTable::getList([
            'filter' => ['ID' => $USER->getId()],
            'select' => ['UF_FAVORIT_INDUSTRIAL_SERVICES'],
        ])->fetch()['UF_FAVORIT_INDUSTRIAL_SERVICES'], true);

        if(count($favoriteIds)>4){
            return json_encode([
                'status' => 0,
                'error' => 'Достигнуто максимальное число избранных сервисов'
            ]);
        }

        $favoriteIds[] = $id;

        $USER->Update($USER->getId(), ['UF_FAVORIT_INDUSTRIAL_SERVICES' => json_encode(array_unique($favoriteIds))]);

        ob_start();

        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            "rs:industrial.services",
            "",
            [],
            null,
            array("HIDE_ICONS" => "Y")
        );

        $html = ob_get_clean();

        return json_encode([
            'status' => 1,
            'html' => $html
        ]);

	}

    function defineFavoriteServicesIndents(){
        global $USER;

        if(!$USER->getId()){
            ShowMessage('Необходимо авторизироваться');
            return;
        }

        return json_decode(UserTable::getList([
            'filter' => ['ID' => $USER->getId()],
            'select' => ['UF_FAVORIT_INDUSTRIAL_SERVICES'],
        ])->fetch()['UF_FAVORIT_INDUSTRIAL_SERVICES'], true);
    }


    protected function defineServices(){

        if(!loader::includeModule('iblock')){
            ShowMessage('Модуль iblock отсутствует');
            return;
        }


        global $USER;

        if(1==2){
            $projectUpdatesTable = HighloadBlockTable::compileEntity(HighloadBlockTable::getById(WORKPROJECTS_UPLOADS_HIGHLOAD_ID)->fetch())->getDataClass();
            $updates = array_column($projectUpdatesTable::getList(['select' => ['UF_ID'], 'filter' => ['UF_ENTITY' => 'INDUSTRIAL_SERVICES', 'UF_USERS' => $USER->getId()]])->fetchAll(), 'UF_ID');
        }else
            $updates = [];


        $favoriteIds = $this->defineFavoriteServicesIndents();

        $table = \Bitrix\Iblock\Iblock::wakeUp(1)->getEntityDataClass();

        $this->arResult['SERVICES'] = $table::getList([
            'select' => ['SERVICE_LINK' => 'LINK.VALUE', 'NAME', 'ID', 'PREVIEW_TEXT'],
            'order' => ['NAME' => 'asc']
        ])->fetchAll();

        if($favoriteIds || $updates) {
            $this->arResult['SERVICES'] = array_map(function ($v) use ($favoriteIds, $updates) {

                if(array_search($v['ID'], $updates) !== false)
                    $v['IS_NEW'] = 1;

                if (in_array($v['ID'], $favoriteIds))
                    $v['IS_FAVORITE'] = true;
                return $v;
            }, $this->arResult['SERVICES']);

            $this->arResult['FAVORITE_SERVICES'] = array_filter($this->arResult['SERVICES'], fn($v) => $v['IS_FAVORITE'] == true);
        }

    }


    public function executeComponent() {

        $this->defineServices();

		$this->IncludeComponentTemplate();
    }
}
