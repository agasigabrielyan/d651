<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Orm;

class RsEntityEdit extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){return[];}

    public static function loadEntityAction(){
        if (!Loader::includeModule("gpi.workproject"))
            return;

        $request = Application::getInstance()->getContext()->getRequest();
        $data = $request->getPostList()->toArray();
        $params = Bitrix\Main\Web\Json::decode($data['RS_COMPONENT_PARAMS']);

        return Helper\FormData::save($params['TABLE'], [], 1);
    }

    public static function deleteEntityAction($id, $module, $type, $tableName){
        if(!\Bitrix\Main\Loader::IncludeModule($module))
            return;

        $tableName::delete($id);
    }


    function includeModuls() {
        $this->arParams['MODULS'][] ='fileman';
        foreach ($this->arParams['MODULS'] as $moduleId){
            if(!Loader::IncludeModule($moduleId))
                return;
        }
    }

    function constructColumns(){



        if($this->arParams['ID'])
            $elementData = $this->arParams['TABLE']::getById($this->arParams['ID'])->fetch();

        $this->arResult['ENTITY_DATA'] = $elementData;

        $this->arParams['COLUMNS'][] = ['HTML' => "<input type='hidden' name='RS_COMPONENT_PARAMS' value='".Bitrix\Main\Web\Json::encode($this->arParams)."'>"];
        $this->arParams['COLUMNS'][] = ['HTML' => '<input type="hidden" name="ID" value="'.$this->arParams['ID'].'">'];

        foreach ($this->arParams['COLUMNS'] as $key => $column){

            $required = $column['REQUIRED']? 'required' : '';

            switch ($column['TYPE']){
                case 'number':
                    $this->arParams['COLUMNS'][$key]['HTML'] = "
                        <legend>
                            <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                            <input class='field' $required type='number' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                        </legend>
                    ";
                    break;

                case 'select':

                    $options=[];
                    foreach ($column['ITEMS'] as $item) {
                        if($column['MULTIPLE'] =='Y')
                            $selected = in_array($item['ID'], $elementData[$column['CODE']])? 'selected' : '';
                        else
                            $selected = $elementData[$column['CODE']] == $item['ID']? 'selected' : '';

                        $options[] = "<option {$selected} value='{$item['ID']}'>{$item['VALUE']}</option>";
                    }
                    $options=implode('', $options);

                    $this->arParams['COLUMNS'][$key]['HTML'] = "
                        <legend>
                            <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                            <select class='field' $required name='{$column['CODE']}'>{$options}</select>
                        </legend>
                        <script>
                        new SlimSelect({
                            select : '[name={$column['CODE']}]',
                            settings: {
                                placeholderText: '',
                                searchText: 'Найти',
                                searchPlaceholder: 'Найти',
                                hideSelected: false,
                                closeOnSelect: false,
                            }
                        });
                        </script>
                    ";
                    break;

                case 'html':

                    ob_start();

                    $LHE = new CHTMLEditor;
                    $LHE->Show(array(
                        'name' => $column['CODE'],
                        'id' => $column['CODE'],
                        'inputName' => $column['CODE'],
                        'content' => $elementData[$column['CODE']],
                        'width' => '100%',
                        'minBodyWidth' => 350,
                        'normalBodyWidth' => 555,
                        'height' => '100',
                        'bAllowPhp' => false,
                        'limitPhpAccess' => false,
                        'autoResize' => true,
                        'autoResizeOffset' => 40,
                        'useFileDialogs' => false,
                        'saveOnBlur' => true,
                        'showTaskbars' => false,
                        'showNodeNavi' => false,
                        'askBeforeUnloadPage' => true,
                        'bbCode' => false,
                        'siteId' => SITE_ID,
                        'controlsMap' => array(
                            array('id' => 'Bold', 'compact' => true, 'sort' => 80),
                            array('id' => 'Italic', 'compact' => true, 'sort' => 90),
                            array('id' => 'Underline', 'compact' => true, 'sort' => 100),
                            array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
                            array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
                            array('id' => 'Color', 'compact' => true, 'sort' => 130),
                            array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
                            array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
                            array('separator' => true, 'compact' => false, 'sort' => 145),
                            array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
                            array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
                            array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
                            array('separator' => true, 'compact' => false, 'sort' => 200),
                            array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
                            array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
                            array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
                            array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
                            array('separator' => true, 'compact' => false, 'sort' => 290),
                            array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
                            array('id' => 'More', 'compact' => true, 'sort' => 400)
                        ),
                    ));

                    $html = ob_get_clean();
                    $this->arParams['COLUMNS'][$key]['HTML'] = "
                        <legend>
                            <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                            $html
                        </legend>
                    ";
                    break;

                case 'string':
                    if($column['MULTIPLE'] =='Y'){
                        $inputs = [];
                        foreach (is_array($elementData[$column['CODE']])? $elementData[$column['CODE']] : [''] as $str){
                            $inputs[] = "<div class='d-flex'><input class='mb-1' type='text' name='{$column['CODE']}[]' value='$str'> <span onclick='this.parentNode.remove()' style='cursor: pointer' class='ml-1'>x</span></div>";
                        }
                        $inputs = implode('', $inputs);

                        $this->arParams['COLUMNS'][$key]['HTML'] = "
                            <legend>
                                <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                                $inputs
                                <div class='ui-btn ui-btn-sm' onclick='addMultipleStringRow(this)'>Добавить</div>
                                <input value='Y' type='hidden' name='{$column['CODE']}_IS_MULTIPLE_STRING'>
                            </legend>
                        ";
                    }else
                        $this->arParams['COLUMNS'][$key]['HTML'] = "
                            <legend>
                                <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                                <input class='field' $required type='text' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                            </legend>
                        ";
                    break;

                case 'hidden':
                    $this->arParams['COLUMNS'][$key]['HTML'] = "
                         <input class='field' type='hidden' value='{$column['VALUE']}' name='{$column['CODE']}'>
                    ";
                    break;

                case 'date':
                    $this->arParams['COLUMNS'][$key]['HTML'] = "
                         <legend>
                            <label for='{$column['CODE']}' onclick='BX.calendar({node:  this.parentNode.querySelector(\"input\") , field:  this.parentNode.querySelector(\"input\") , bTime:  false , bSetFocus:  true});'>{$column['TITLE']}</label>
                            <input class='field' onclick='BX.calendar({node:  this , field:  this , bTime:  false , bSetFocus:  true});' $required type='string' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                        </legend>
                    ";
                    break;

                case 'file':
                        if($column['MULTIPLE'] =='Y') {
                            $files = $elementData[$column['CODE']] ? \Bitrix\Main\Web\Json::encode(Bitrix\Main\FileTable::GetList(['filter' => ['ID' => $elementData[$column['CODE']]]])->fetchAll()) : [];
                            $this->arParams['COLUMNS'][$key]['HTML'] = "
                                 <legend>
                                    <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                                    <input multiple type='file' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                                    <script>
                                    BX.ready(function(){
                                        new BearFileInput(document.querySelector('input[name=\"{$column['CODE']}\"]'), ".$files.");
                                    })
                                    </script>
                                </legend>
                            ";
                        }else{
                            $files = $elementData[$column['CODE']] ? \Bitrix\Main\Web\Json::encode(CFile::GetList(['filter' => ['ID' => $elementData[$column['CODE']]]])->fetch()) : [];
                            $this->arParams['COLUMNS'][$key]['HTML'] = "
                                 <legend>
                                    <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                                    <input type='file' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                                    <script>
                                    BX.ready(function(){
                                        new BearFileInput(document.querySelector('input[name=\"{$column['CODE']}\"]'), [".$files."]);
                                    })
                                    </script>
                                </legend>
                            ";
                        }
                    break;

                case 'user':
                    $usersList = Bitrix\Main\Web\Json::encode($column['LIST']);
                    $this->arParams['COLUMNS'][$key]['HTML'] = "
                        <legend>
                            <label for='{$column['CODE']}'>{$column['TITLE']}</label>
                            <input class='field' $required type='text' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                        </legend>
                    ";
                    $this->arParams['COLUMNS'][$key]['HTML'].="
                    <script>
                        let renderPlace = document.querySelector('input[name={$column['CODE']}]');
                        let usersList = {$usersList};
                        let arFields = {
                            id: {$key},
                            multiple: false,
                            dialogOptions: {
                                context: 'MY_MODULE_CONTEXT',
                                items: usersList,
                                tabs:[ {
                                    id: 'US_LIST',
                                    title: 'Пользователи',
                                    itemOrder: {title:'asc'},
                                }]
                            },
                            events: {
                                onAfterTagAdd: function (event) {
                                    console.log(event.getTarget().getContainer());
                                    let userId = event.getData().tag.id;
                                    document.querySelector('input[name={$column['CODE']}]').value = userId;
                    
                                },
                                onTagRemove: function (event) {
                                    document.querySelector('input[name={$column['CODE']}]').value = '';
                                }
                            }
                        };
                        if(parseInt(renderPlace.value)>0){
                            arFields.items = [
                                {
                                    id : renderPlace.value,
                                    title : usersList[usersList.findIndex(x=>x.id == renderPlace.value)].title,
                                    entityId : 'userB',
                                }
                            ]
                        }
                    
                        let tagSelector = new BX.UI.EntitySelector.TagSelector(arFields);
                        renderPlace.hidden=true;
                    
                        tagSelector.renderTo(renderPlace.parentNode);
                    </script>
                    ";
                    break;
            }
        }
    }

    function defineParams(){
        global $APPLICATION;
        CJSCore::Init(array('rs.buttons', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list', 'ui.dialogs.messagebox', 'sidepanel.reference.link.save', 'bear.file.input', 'ui.entity-selector', 'slimselect'));



        $elementName = $this->arResult['ENTITY_DATA']['TITLE'];
        if(!$elementName)
            $elementName = $this->arResult['ENTITY_DATA']['NAME'];
        if(!$elementName)
            $elementName = $this->arResult['ENTITY_DATA']['UF_TITLE'];
        if(!$elementName)
            $elementName = $this->arResult['ENTITY_DATA']['UF_NAME'];
        if(!$elementName)
            $elementName = '...';

        $context = Bitrix\Main\Application::getInstance()->getContext();
        $server = $context->getServer();

        $correctUrl = substr($server['REQUEST_URI'], 0, strpos($server['REQUEST_URI'],'?')?? strlen($server['REQUEST_URI']));
        if(!$correctUrl)
            $correctUrl = $server['REQUEST_URI'];

        $APPLICATION->setTitle("Редактирование элемента: {$elementName}");
        $APPLICATION->AddChainItem("Редактирование элемента: {$elementName}", $correctUrl);
        $APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
    }

    public function executeComponent() {

        if(!$this->arParams['TABLE'] || !$this->arParams['MODULS'] || !$this->arParams['COLUMNS'])
            return;


        $this->includeModuls();
        $this->constructColumns();

        $this->defineParams();

        $this->includeComponentTemplate();
    }
}
