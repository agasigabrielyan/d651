<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper,
    Bitrix\Highloadblock as HL,
    Gpi\Workproject\Orm;

class BndingEditor extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){return[];}

    public static function loadEntityAction(){

        $request = Application::getInstance()->getContext()->getRequest();
        $data = $request->getPostList()->toArray();
        $params = Bitrix\Main\Web\Json::decode($data['RS_COMPONENT_PARAMS']);

        foreach ($params['MODULS'] as $moduleId){
            if(!Loader::IncludeModule($moduleId))
                return;
        }

        return self::save($params['TABLE'], [], 1);
    }

    public static function deleteEntityAction($id, $module, $type, $tableName){
        if(!\Bitrix\Main\Loader::IncludeModule($module))
            return;

        $tableName::delete($id);
    }

    public static function parse($dateTimeFields = [], $setMemberChanges=0){
        $request = Application::getInstance()->getContext()->getRequest();
        $data = $request->getPostList()->toArray();
        $files = $request->getFileList()->toArray();
        $params = Bitrix\Main\Web\Json::decode($data['RS_COMPONENT_PARAMS']);
        unset($data['RS_COMPONENT_PARAMS']);

        foreach (array_filter($data, fn($v) => $v == 'function item() { [native code] }') as $key => $value){

            $key = substr($key, 0 , mb_strpos($key, 'item'));
            $data[$key] = explode(',', $data[$key.'_POST_LIST']);

            foreach ($files as $fileKey => $fileArray){
                if(strpos($fileKey, $key) === 0 && intval(substr($fileKey, 0, strlen($key))) == substr($fileKey, 0, strlen($key)))
                    $data[$key][] = $fileArray;
            }

            if($data[$key.'_IS_MULTIPLE'] == 'false'){

                $data[$key] = array_filter($data[$key]);
                $data[$key] = array_pop($data[$key]);

            }

            unset($data[$key.'length'], $data[$key.'item'], $data[$key.'_POST_LIST'],$files[$key], $data[$key.'_IS_MULTIPLE']);
        }

        foreach (array_filter($params['COLUMNS'], fn($v) => $v['MULTIPLE'] == 'Y') as $key => $value){
            $data[$key] = array_filter(explode(',', $data[$key]), fn($v) => $v != '');
        }

        return $data;
    }

    public static function save($table, $dateTimeFields=[], $setMemberChanges=0){

        $data = self::parse();
        self::correctPropsData($data, $table);

        if($setMemberChanges){
            global $USER;
            $data['UPDATED_BY'] = $USER->getId();
            $data['UPDATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());
        }

        foreach ($dateTimeFields as $propCode) {
            if ($data[$propCode])
                $data[$propCode] = \Bitrix\Main\Type\Datetime::createFromTimestamp(strtotime($data[$propCode]));
        }

        $request = Application::getInstance()->getContext()->getRequest();
        $data2 = $request->getPostList()->toArray();
        $params = Bitrix\Main\Web\Json::decode($data2['RS_COMPONENT_PARAMS']);

        if(in_array('iblock', $params['MODULS'])){
            $fields = [
                'ID',
                'TIMESTAMP_X',
                'MODIFIED_BY',
                'DATE_CREATE',
                'CREATED_BY',
                'IBLOCK_ID',
                'IBLOCK_SECTION_ID',
                'ACTIVE',
                'ACTIVE_FROM',
                'ACTIVE_TO',
                'SORT',
                'NAME',
                'PREVIEW_PICTURE',
                'PREVIEW_TEXT',
                'PREVIEW_TEXT_TYPE',
                'DETAIL_PICTURE',
                'DETAIL_TEXT',
                'DETAIL_TEXT_TYPE',
                'SEARCHABLE_CONTENT',
                'WF_STATUS_ID',
                'WF_PARENT_ELEMENT_ID',
                'WF_NEW',
                'WF_LOCKED_BY',
                'WF_DATE_LOCK',
                'WF_COMMENTS',
                'IN_SECTIONS',
                'XML_ID',
                'CODE',
                'TAGS',
                'TMP_ID',
                'SHOW_COUNTER',
                'SHOW_COUNTER_START',
            ];
            foreach ($data as $key => $value) {
                if (!in_array($key, $fields)) {
                    $props[$key] = $value;
                    unset($data[$key]);
                }
            }
            $el = new CIBlockElement;
            if (intval($data['ID']) == 0)
                $PRODUCT_ID = $el->Add($data);
            else{
                $el->Update($data['ID'], $data);
                $PRODUCT_ID = $data['ID'];
            }

            if($PRODUCT_ID){
                CIBlockElement::SetPropertyValuesEx($PRODUCT_ID, false, $props);
                return [
                    'status' => 1,
                ];
            }
            else
                return [
                    'status' => 0,
                    'error' => 'Ошибка: ' . $el->LAST_ERROR,
                ];
        }else{
            if (intval($data['ID']) > 0){

                $id = $data['ID'];
                unset($data['ID']);
                $addResult = $table::update($id, $data);
            }else{

                unset($data['ID']);
                if($setMemberChanges){
                    global $USER;
                    $data['CREATED_BY'] = $USER->getId();
                    $data['CREATED_TIME'] = \Bitrix\Main\Type\Datetime::createFromTimestamp(time());
                }

                $addResult = $table::add($data);
            }

            if($addResult->isSuccess())
                return [
                    'status' => 1,
                    'elemenId' => $addResult->getID(),
                ];
            else
                return [
                    'status' => 0,
                    'error' => 'Ошибка: ' . implode(', ', $addResult->getErrors()),
                ];
        }
    }

    public static function correctPropsData(&$data, $table){
        $fieldMap = [];
        foreach($table::getMap() as $field){
            $fieldMap[$field->getTitle()] = [
                'TYPE' => $field->getDataType(),
            ];
        }

        foreach (array_filter($fieldMap, fn($v) => $v['TYPE'] == 'datetime' || $v['TYPE'] == 'date') as $key => $propInfo)
            if($data[$key])
                $data[$key] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($data[$key]));
    }


    function defineQuery($iblock){
        if(!Loader::IncludeModule('iblock'))
            return;
        $this->query=[
            'select' => [
                'NAME',
                'ID'
            ],
        ];
        $directories=[];
        $properties = CIBlockProperty::GetList(
            [
                "sort" => "asc",
                "name" => "asc"
            ],
            [
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $iblock
            ]
        );
        while ($prop = $properties->GetNext()) {

            $this->arParams['PROPS'][$prop['CODE']] = [
                'CODE' => $prop['CODE'],
                'NAME' => $prop['NAME'],
                'TYPE' => $prop['USER_TYPE'] ?? $prop['PROPERTY_TYPE'],
                'LINK_IBLOCK_ID' => $prop['LINK_IBLOCK_ID'],
                'MULTIPLE' => $prop['MULTIPLE']
            ];
            if($prop['USER_TYPE'] == 'directory')
                $directories[$prop['CODE']] = $prop['USER_TYPE_SETTINGS']['TABLE_NAME'];

            if($this->arParams['PROPS'][$prop['CODE']]['TYPE'] == 'E')
                $this->query['select']["{$prop['CODE']}_V"] = "{$prop['CODE']}.IBLOCK_GENERIC_VALUE";
            else
                $this->query['select']["{$prop['CODE']}_V"] = "{$prop['CODE']}.VALUE";
        }
        $codes = array_keys($directories);
        $tables = array_values($directories);

        if(count($directories)>0){
            $hlds = Bitrix\Highloadblock\HighloadBlockTable::getList([
                'filter' => [
                    'TABLE_NAME' => $tables
                ]
            ]);
            while($hld = $hlds->fetch()) {
                $indexes = array_keys($tables, $hld['TABLE_NAME']);
                foreach ($indexes as $index)
                    $this->arParams['PROPS'][$codes[$index]]['HIGHLOAD_ID'] = $hld['ID'];
            }
        }
        foreach (array_filter($this->arParams['PROPS'], fn($v) => $v['TYPE'] == 'F') as $prop){
            $this->query['runtime']["{$prop['CODE']}_FILE"] = [
                'data_type' => 'Bitrix\Main\FileTable',
                'reference' => ["this.{$prop['CODE']}.VALUE" => 'ref.ID'],
            ];
            $this->query['runtime'][] = New Bitrix\Main\Entity\ExpressionField(
                "{$prop['CODE']}_LINK",
                'CONCAT("/upload/", %s, "/", %s)',
                ["{$prop['CODE']}_FILE.SUBDIR", "{$prop['CODE']}_FILE.FILE_NAME"]
            );
            $this->query['select'][] = "{$prop['CODE']}_LINK";
            $this->query['select']["{$prop['CODE']}_NAME"] = "{$prop['CODE']}_FILE.ORIGINAL_NAME";
        }
        foreach (array_filter($this->arParams['PROPS'], fn($v) => $v['TYPE'] == 'E') as $code => $prop){//инфоблок
            $this->arParams['PROPS'][$code]['ENTITY_DATA_CLASS'] = \Bitrix\Iblock\Iblock::wakeUp($prop['LINK_IBLOCK_ID'])->getEntityDataClass();
            $this->query['runtime']["{$prop['CODE']}_IBLOCK"] = [
                'data_type' => $this->arParams['PROPS'][$code]['ENTITY_DATA_CLASS'],
                'reference' => ["this.{$prop['CODE']}.VALUE" => 'ref.ID']
            ];
            $this->query['select']["{$prop['CODE']}_NAME"] = "{$prop['CODE']}_IBLOCK.NAME";
        }
        foreach (array_filter($this->arParams['PROPS'], fn($v) => $v['TYPE'] == 'UserID') as $prop){
            $this->query['runtime']["{$prop['CODE']}_USER"] = [
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => ["this.{$prop['CODE']}.VALUE" => 'ref.ID']
            ];
            $this->query['runtime'][] = new \Bitrix\Main\Entity\ExpressionField(
                "{$prop['CODE']}_USER_LAST_NAME",
                'COALESCE(%s, " ")',
                "{$prop['CODE']}_USER.LAST_NAME"
            );
            $this->query['runtime'][] = new \Bitrix\Main\Entity\ExpressionField(
                "{$prop['CODE']}_USER_NAME",
                'COALESCE(%s, " ")',
                "{$prop['CODE']}_USER.NAME"
            );
            $this->query['runtime'][] = new \Bitrix\Main\Entity\ExpressionField(
                "{$prop['CODE']}_USER_SECOND_NAME",
                'COALESCE(%s, " ")',
                "{$prop['CODE']}_USER.SECOND_NAME"
            );
            $this->query['runtime'][] = new \Bitrix\Main\Entity\ExpressionField(
                "{$prop['CODE']}_USER_FIO",
                'CONCAT(%s, " ", %s, " ", %s)',
                ["{$prop['CODE']}_USER_LAST_NAME","{$prop['CODE']}_USER_NAME", "{$prop['CODE']}_USER_SECOND_NAME"]
            );
            $this->query['select'][] = "{$prop['CODE']}_USER_FIO";
        }
        foreach (array_filter($this->arParams['PROPS'], fn($v) => $v['TYPE'] == 'directory') as $code => $prop){
            $this->arParams['PROPS'][$code]['ENTITY_DATA_CLASS'] = HL\HighloadBlockTable::compileEntity(HL\HighloadBlockTable::getById($prop['HIGHLOAD_ID'])->fetch())->getDataClass();
            $this->query['runtime']["{$prop['CODE']}_HIGHLOAD"] = [
                'data_type' => $this->arParams['PROPS'][$code]['ENTITY_DATA_CLASS'],
                'reference' => ["this.{$prop['CODE']}.VALUE" => 'ref.ID']
            ];
            $this->query['select']["{$prop['CODE']}_NAME"] = "{$prop['CODE']}_HIGHLOAD.UF_NAME";
        }
        foreach (array_filter($this->arParams['PROPS'], fn($v) => $v['TYPE'] == 'L') as $prop){
            $this->query['runtime']["{$prop['CODE']}_ENUM"] = [
                'data_type' => '\Bitrix\Iblock\PropertyEnumerationTable',
                'reference' => ["this.{$prop['CODE']}.VALUE" => 'ref.ID']
            ];
            $this->query['select']["{$prop['CODE']}_TEXT"] = "{$prop['CODE']}_ENUM.VALUE";
        }


        $this->query['runtime'][] = New Bitrix\Main\Entity\ExpressionField(
            'EDIT_LINK',
            "REPLACE('{$this->arParams['URL_TEMPLATES']['brands.list.item.edit']}', '#brand_id#', %s)",
            'ID'
        );
        $this->query['select'][] = 'EDIT_LINK';
        $this->query['runtime'][] = New Bitrix\Main\Entity\ExpressionField(
            'LINK',
            "REPLACE('{$this->arParams['URL_TEMPLATES']['brands.list.item']}', '#brand_id#', %s)",
            'ID'
        );
        $this->query['select'][] = 'LINK';

    }

    function correctElements(&$elements){
        $multipleProps = array_filter($this->arParams['PROPS'], fn($v) => $v['MULTIPLE'] == 'Y');
        if(count($multipleProps)==0)
            return;

        $elementsSwap=[];
        foreach ($elements as $element){
            foreach ($multipleProps as $propInfo){
                $prop = $propInfo['CODE'];

                if(!$swap = $element[$prop. '_V'])
                    return;

                switch ($propInfo['TYPE']){
                    case 'F':
                        $element[$prop] = $elementsSwap[$element['ID']][$prop] ?? [];
                        $element[$prop][$swap] = [
                            'LINK' => $element["{$prop}_LINK"],
                            'TITLE' => $element["{$prop}_NAME"],
                        ];
                        break;

                    case 'E':
                        $element[$prop] = $elementsSwap[$element['ID']][$prop] ?? [];
                        $element[$prop][$swap] = $element["{$prop}_NAME"];
                        break;

                    case 'UserID':
                        $element[$prop] = $elementsSwap[$element['ID']][$prop] ?? [];
                        $element[$prop][$swap] = $element["{$prop}_USER_FIO"];
                        break;

                    case 'directory':
                        $element[$prop] = $elementsSwap[$element['ID']][$prop] ?? [];
                        $element[$prop][$swap] = $element["{$prop}_NAME"];
                        break;

                    case 'L':
                        $element[$prop] = $elementsSwap[$element['ID']][$prop] ?? [];
                        $element[$prop][$swap] = $element["{$prop}_TEXT"];
                        break;

                    default:

                        $element[$prop] = $elementsSwap[$element['ID']][$prop] ?? [];
                        $element[$prop][] = $swap;
                        $element[$prop]= array_unique($element[$prop]);
                        break;
                }
            }
            $elementsSwap[$element['ID']]=$element;
        }
        $elements = $elementsSwap;
    }


    function includeModuls() {
        $this->arParams['MODULS'][] ='fileman';
        foreach ($this->arParams['MODULS'] as $moduleId){
            if(!Loader::IncludeModule($moduleId))
                return;
        }
    }

    function getElementData(){
        if(in_array('iblock', $this->arParams['MODULS'])){
            $this->defineQuery($this->arParams['TABLE']::getEntity()->getIblock()->getId());
            $this->query['filter'] = ['ID' => $this->arParams['ID']];
            $result = $this->arParams['TABLE']::getList($this->query)->fetchAll();
            $this->correctElements($result);
            $element = current($result);

            foreach (array_filter($this->arParams['PROPS'], fn($v) => $v['MULTIPLE'] == 'N') as $prop){
                $element[$prop['CODE']] = $element[$prop['CODE'].'_V'];
            }
            return $element;

        }
        return $this->arParams['TABLE']::getById($this->arParams['ID'])->fetch();
    }

    function constructColumns(){

        global $APPLICATION;

        if($this->arParams['ID'])
            $elementData = $this->getElementData();

        $this->arResult['ENTITY_DATA'] = $elementData;

        $this->arParams['COLUMNS'][] = ['HTML' => "<input type='hidden' name='RS_COMPONENT_PARAMS' value='".Bitrix\Main\Web\Json::encode($this->arParams)."'>"];
        $this->arParams['COLUMNS'][] = ['HTML' => '<input type="hidden" name="ID" value="'.$this->arParams['ID'].'">'];

        foreach ($this->arParams['COLUMNS'] as $key => $column){

            switch ($column['TYPE']){
                case 'number':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getNumberEditorHtml($column, $elementData);
                    break;

                case 'select':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getSelectEditorHtml($column, $elementData);
                    break;

                case 'html':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getHtmlEditorHtml($column, $elementData);
                    break;

                case 'string':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getStringEditorHtml($column, $elementData);
                    break;

                case 'textarea':
                case 'text':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getTextareaEditorHtml($column, $elementData);
                    break;

                case 'hidden':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getHiddenInputHtml($column);
                    break;

                case 'date':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getDateEditorHtml($column, $elementData);
                    break;

                case 'file':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getFileEditorHtml($column, $elementData);
                    break;

                case 'user':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getUserEditorHtml($column, $elementData);
                    break;

                case 'legend':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getLegendHtml($column);
                    break;

                case 'iblock':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getIblockEditorHtml($column, $elementData);
                    break;

                case 'highload':
                    $this->arParams['COLUMNS'][$key]['HTML'] = $this->getHighloadEditorHtml($column, $elementData);
                    break;
            }
        }
    }

    function getTextareaEditorHtml($column, $elementData){
        return "
        <legend>
            <label for='{$column['CODE']}'>{$column['NAME']}</label>
            <textarea class='field ui-ctl ui-ctl-textarea' $required type='number' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>{$elementData[$column['CODE']]}</textarea>
        </legend>
        ";
    }

    function getNumberEditorHtml($column ,$elementData) {

        return "
        <legend>
            <label for='{$column['CODE']}'>{$column['NAME']}</label>
            <input class='field ui-ctl ui-ctl-textbox' $required type='number' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
        </legend>
        ";
    }

    function getSelectEditorHtml($column ,$elementData){
        foreach ($column['ITEMS'] as $id => $value) {
            $elements[] = [
                'id' => $id,
                'entityId' => 'item',
                'title' => $value,
                'tabs' => 'list'
            ];
        }
        $elements = Bitrix\Main\Web\Json::encode($elements);

        $returnHtml = "
            <legend class='{$column['CODE']}'>
                <label for='{$column['CODE']}'>{$column['NAME']}</label>
            </legend>
            <script>
                var renderPlace{$column['CODE']} = document.querySelector('.{$column['CODE']}');
                var params = {
                    textBoxAutoHide: true,
                    textBoxWidth: 350,
                    id: '{$column['CODE']}_DIALOG',
                    maxHeight: 99,
                    dialogOptions:{
                      compactView: true,
                      dropdownMode: true,
                      items: $elements,
                        tabs: [
                            { id: 'list', title: 'Список' }
                        ],
                        showAvatars: false,
                    },
            }
        ";

        switch ($column['MULTIPLE']){
            case 'Y':
                $selected=[];
                foreach($elementData[$column['CODE']] as $id => $value){
                    $selected[] = [
                        'id' => $id,
                        'entityId' => 'list',
                        'title' => $value
                    ];
                    $additHtml.= "<input type='hidden' name='{$column['CODE']}[]' value='{$id}'>";
                }
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            let ids = tagSelector{$column['CODE']}.getTags().map(x => x.id);
                            let inputs = Array.from(renderPlace{$column['CODE']}.querySelectorAll('[name=\"{$column['CODE']}[]\"]'));
                            for(let i in inputs){
                                inputs[i].remove();
                            }

                            for(let i in ids){
                                renderPlace{$column['CODE']}.appendChild(BX.create({
                                    tag : 'input',
                                    attrs: {value:ids[i],},
                                    props: {value:ids[i], name: '{$column['CODE']}[]', hidden: true}
                                }))
                            }
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[value=\"'event.getData().tag.id+'\"]').remove();
                        },
                    };
                    params.items = $selected;
                    params.multiple = true;
                ";
                break;

            default:
                $selected=[];
                if($elementData[$column['CODE']]){
                    $selected[] = [
                        'id' => $elementData[$column['CODE']],
                        'entityId' => 'list',
                        'title' => $elementData[$column['CODE'].'_TEXT']
                    ];
                }
                $additHtml.= "<input type='hidden' name='{$column['CODE']}' value='{$elementData[$column['CODE']]}'>";
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = event.getData().tag.id;
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = '';
                        }
                    };
                    params.items = $selected;
                    params.multiple = false;
                ";
                break;
        }

        $returnHtml.="
                let tagSelector{$column['CODE']} = new BX.UI.EntitySelector.TagSelector(params);
                tagSelector{$column['CODE']}.renderTo(renderPlace{$column['CODE']});
            </script>
        ".$additHtml;

        return $returnHtml;
    }

    function getHtmlEditorHtml($column, $elementData){
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
        return "
            <legend>
                <label for='{$column['CODE']}'>{$column['NAME']}</label>
                $html
            </legend>
        ";
    }

    function getStringEditorHtml($column, $elementData){

        if($column['MULTIPLE'] =='Y'){
            $inputs = [];
            foreach (is_array($elementData[$column['CODE']])? $elementData[$column['CODE']] : [''] as $str){
                $inputs[] = "<div class='d-flex'><input class='mb-1 ui-ctl ui-ctl-textbox' type='text' name='{$column['CODE']}[]' value='$str'> <span onclick='this.parentNode.remove()' style='cursor: pointer' class='ml-1'>x</span></div>";
            }
            $inputs = implode('', $inputs);

            $returnHtml = "
                <legend>
                    <label for='{$column['CODE']}'>{$column['NAME']}</label>
                    $inputs
                    <div class='ui-btn ui-btn-sm' onclick='addMultipleStringRow(this)'>Добавить</div>
                    <input value='Y' type='hidden' name='{$column['CODE']}_IS_MULTIPLE_STRING'>
                </legend>
            ";
        }else
            $returnHtml = "
                    <legend>
                        <label for='{$column['CODE']}'>{$column['NAME']}</label>
                        <input class='field ui-ctl ui-ctl-textbox' $required type='text' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                    </legend>
                ";

        return $returnHtml;
    }

    function getHiddenInputHtml($column){
        $returnData = "
             <input class='field' type='hidden' value='{$column['VALUE']}' name='{$column['CODE']}'>
        ";

        return $returnData;
    }

    function getDateEditorHtml($column, $elementData){
        if(is_string($elementData[$column['CODE']]))
            if(strlen($elementData[$column['CODE']])>4)
                $elementData[$column['CODE']] = date('d.m.Y', strtotime($elementData[$column['CODE']]));

        $returnHtml = "
             <legend>
                <label for='{$column['CODE']}' onclick='BX.calendar({node:  this.parentNode.querySelector(\"input\") , field:  this.parentNode.querySelector(\"input\") , bTime:  false , bSetFocus:  true});'>{$column['NAME']}</label>
                <input class='field ui-ctl ui-ctl-textbox' onclick='BX.calendar({node:  this , field:  this , bTime:  false , bSetFocus:  true});' $required type='string' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
            </legend>
        ";

        return $returnHtml;
    }

    function getFileEditorHtml($column, $elementData){
        switch ($column['MULTIPLE']) {
            case 'Y':
                $files = $elementData[$column['CODE']] ? \Bitrix\Main\Web\Json::encode(Bitrix\Main\FileTable::GetList(['filter' => ['ID' => $elementData[$column['CODE']]]])->fetchAll()) : [];

                $returnHtml = "
                    <legend>
                        <label for='{$column['CODE']}'>{$column['NAME']}</label>
                        <input multiple type='file' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                        <script>
                        BX.ready(function(){
                            new BearFileInput(document.querySelector('input[name=\"{$column['CODE']}\"]'), " . $files . ");
                        })
                        </script>
                    </legend>
                ";
                break;

            default:
                $files = $elementData[$column['CODE']] ? \Bitrix\Main\Web\Json::encode(CFile::GetList(['filter' => ['ID' => $elementData[$column['CODE']]]])->fetch()) : [];

                $returnHtml = "
                    <legend>
                        <label for='{$column['CODE']}'>{$column['NAME']}</label>
                        <input type='file' value='{$elementData[$column['CODE']]}' name='{$column['CODE']}'>
                        <script>
                        BX.ready(function(){
                            new BearFileInput(document.querySelector('input[name=\"{$column['CODE']}\"]'), [" . $files . "]);
                        })
                        </script>
                    </legend>
                ";
                break;
        }

        return $returnHtml;
    }

    function getLegendHtml($column){
        $returnHtml = '<div class="difference">'.$column['NAME'].'</div>';

        return $returnHtml;
    }

    function getUserEditorHtml($column, $elementData){

        $returnHtml = "
            <legend class='{$column['CODE']}'>
                <label for='{$column['CODE']}'>{$column['NAME']}</label>
            </legend>
            <script>
                let renderPlace{$column['CODE']} = document.querySelector('.{$column['CODE']}');
                var params = {
                    id: '{$column['CODE']}_DIALOG',
                    multiple: false,
                    dialogOptions:
                    {
                        compactView: true,
                        context: 'MY_MODULE_CONTEXT',
                        entities: [
                            {
                                id: 'user', // пользователи
                            },
                            {
                                id: 'meta-user',
                                options: {
                                    'all-users': true // Все сотрудники
                                }
                            },
                        ],
                    },
            }
        ";

        switch ($column['MULTIPLE']){
            case 'Y':
                $selected=[];
                foreach($elementData[$column['CODE']] as $id => $value){
                    $selected[] = [
                        'id' => $id,
                        'entityId' => 'user',
                        'title' => $value
                    ];
                }
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            let ids = tagSelector{$column['CODE']}.getTags().map(x => x.id);
                            let inputs = Array.from(renderPlace{$column['CODE']}.querySelectorAll('[name=\"{$column['CODE']}[]\"]'));
                            for(let i in inputs){
                                inputs[i].remove();
                            }

                            for(let i in ids){
                                renderPlace{$column['CODE']}.appendChild(BX.create({
                                    tag : 'input',
                                    attrs: {value:ids[i],},
                                    props: {value:ids[i], name: '{$column['CODE']}[]', hidden: true}
                                }))
                            }
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[value=\"'+event.getData().tag.id+'\"]').remove();
                        },
                    };
                    params.items = $selected;
                    params.multiple = true;
                ";
                break;

            default:
                $selected=[];
                if($elementData[$column['CODE']]){
                    $selected[] = [
                        'id' => $elementData[$column['CODE']],
                        'entityId' => 'user',
                        'title' => $elementData[$column['CODE'].'_USER_FIO']
                    ];
                }
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = event.getData().tag.id;
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = '';
                        }
                    };
                    params.items = $selected;
                    params.multiple = false;
                ";
                break;
        }

        $returnHtml.="
                let tagSelector{$column['CODE']} = new BX.UI.EntitySelector.TagSelector(params);
                tagSelector{$column['CODE']}.renderTo(renderPlace{$column['CODE']});
            </script>
        ";

        return $returnHtml;
    }

    function getIblockEditorHtml($column, $elementData){
        $elements=[];
        $iblockElementTitles=[];

        $table = \Bitrix\Iblock\Iblock::wakeUp($column['IBLOCK_ID'])->getEntityDataClass();
        $tableItems = $table::getList([
            'select' => ['ID', 'NAME']
        ]);
        while($item = $tableItems->fetch()){
            $elements[] = [
                'id' => $item['ID'],
                'entityId' => $item['ID'],
                'title' => $item['NAME'],
                'tabs' => 'list'
            ];
            $iblockElementTitles[$item['ID']] = $item['NAME'];
        }

        $elements = Bitrix\Main\Web\Json::encode($elements);

        $returnHtml = "
            <legend class='{$column['CODE']}'>
                <label for='{$column['CODE']}'>{$column['NAME']}</label>
            </legend>
            <script>
                let renderPlace{$column['CODE']} = document.querySelector('.{$column['CODE']}');
                var params = {
                    textBoxAutoHide: true,
                    textBoxWidth: 350,
                    id: '{$column['CODE']}_DIALOG',
                    maxHeight: 99,
                    dialogOptions:{
                      compactView: true,
                      items: $elements,
                        tabs: [
                            { id: 'list', title: 'Список' }
                        ],
                        showAvatars: false,
                        dropdownMode: true
                    },
            }
        ";

        switch ($column['MULTIPLE']){
            case 'Y':
                $selected=[];
                foreach($elementData[$column['CODE']] as $id => $value){
                    $selected[] = [
                        'id' => $id,
                        'entityId' => 'list',
                        'title' => $value
                    ];
                    $additHtml.= "<input type='hidden' name='{$column['CODE']}[]' value='{$id}'>";
                }
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            let ids = tagSelector{$column['CODE']}.getTags().map(x => x.id);
                            let inputs = Array.from(renderPlace{$column['CODE']}.querySelectorAll('[name=\"{$column['CODE']}[]\"]'));
                            for(let i in inputs){
                                inputs[i].remove();
                            }

                            for(let i in ids){
                                renderPlace{$column['CODE']}.appendChild(BX.create({
                                    tag : 'input',
                                    attrs: {value:ids[i],},
                                    props: {value:ids[i], name: '{$column['CODE']}[]', hidden: true}
                                }))
                            }
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[value=\"'+event.getData().tag.id+'\"]').remove();
                        },
                    };
                    params.items = $selected;
                    params.multiple = true;
                ";
                break;

            default:
                $selected=[];
                if($elementData[$column['CODE']]){
                    $selected[] = [
                        'id' => $elementData[$column['CODE']],
                        'entityId' => 'list',
                        'title' => $elementData[$column['CODE'].'_NAME']
                    ];
                }
                $additHtml.= "<input type='hidden' name='{$column['CODE']}' value='{$elementData[$column['CODE']]}'>";
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = event.getData().tag.id;
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = '';
                        }
                    };
                    params.items = $selected;
                    params.multiple = false;
                ";
                break;
        }

        $returnHtml.="
                let tagSelector{$column['CODE']} = new BX.UI.EntitySelector.TagSelector(params);
                tagSelector{$column['CODE']}.renderTo(renderPlace{$column['CODE']});
            </script>
        ".$additHtml;

        return $returnHtml;
    }

    function getHighloadEditorHtml($column, $elementData){
        $elements=[];
        $table = HL\HighloadBlockTable::compileEntity(HL\HighloadBlockTable::getById($column['HIGHLOAD_ID'])->fetch())->getDataClass();
        $tableItems = $table::getList([
            'select' => ['ID', 'UF_NAME']
        ]);
        while($item = $tableItems->fetch()){
            $elements[] = [
                'id' => $item['ID'],
                'entityId' => $item['ID'],
                'title' => $item['UF_NAME'],
                'tabs' => 'list'
            ];
        }

        $elements = Bitrix\Main\Web\Json::encode($elements);
        $returnHtml = "
            <legend class='{$column['CODE']}'>
                <label for='{$column['CODE']}'>{$column['NAME']}</label>
            </legend>
            <script>
                let renderPlace{$column['CODE']} = document.querySelector('.{$column['CODE']}');
                var params = {
                    textBoxAutoHide: true,
                    textBoxWidth: 350,
                    id: '{$column['CODE']}_DIALOG',
                    maxHeight: 99,
                    dialogOptions:{
                      compactView: true,
                      items: $elements,
                        tabs: [
                            { id: 'list', title: 'Список' }
                        ],
                        showAvatars: false,
                        dropdownMode: true
                    },
                }
        ";

        switch ($column['MULTIPLE']){
            case 'Y':
                $selected=[];
                foreach($elementData[$column['CODE']] as $id => $value){
                    $selected[] = [
                        'id' => $id,
                        'entityId' => 'list',
                        'title' => $value
                    ];
                    $additHtml.= "<input type='hidden' name='{$column['CODE']}[]' value='{$id}'>";
                }
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            let ids = tagSelector{$column['CODE']}.getTags().map(x => x.id);
                            let inputs = Array.from(renderPlace{$column['CODE']}.querySelectorAll('[name=\"{$column['CODE']}[]\"]'));
                            for(let i in inputs){
                                inputs[i].remove();
                            }

                            for(let i in ids){
                                renderPlace{$column['CODE']}.appendChild(BX.create({
                                    tag : 'input',
                                    attrs: {value:ids[i],},
                                    props: {value:ids[i], name: '{$column['CODE']}[]', hidden: true}
                                }))
                            }
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[value=\"'+event.getData().tag.id+'\"]').remove();
                        },
                    };
                    params.items = $selected;
                    params.multiple = true;
                ";
                break;

            default:
                $selected=[];
                if($elementData[$column['CODE']]){
                    $selected[] = [
                        'id' => $elementData[$column['CODE']],
                        'entityId' => 'list',
                        'title' => $elementData[$column['CODE'].'_NAME']
                    ];
                }
                $additHtml.= "<input type='hidden' name='{$column['CODE']}' value='{$elementData[$column['CODE']]}'>";
                $selected = Bitrix\Main\Web\Json::encode($selected);
                $returnHtml.="
                    params.events = {
                        onAfterTagAdd: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = event.getData().tag.id;
                        },
                        onTagRemove: function (event) {
                            document.querySelector('input[name={$column['CODE']}]').value = '';
                        }
                    };
                    params.items = $selected;
                    params.multiple = false;
                ";
                break;
        }

        $returnHtml.="
                let tagSelector{$column['CODE']} = new BX.UI.EntitySelector.TagSelector(params);
                tagSelector{$column['CODE']}.renderTo(renderPlace{$column['CODE']});
            </script>
        ".$additHtml;

        return $returnHtml;
    }

    function defineParams(){
        global $APPLICATION;
        CJSCore::Init([
            'fx',
            'rs.buttons',
            'ui.notification',
            'ui.buttons',
            'ui.buttons.icons',
            "ui.forms",
            'ui.list',
            'ui.dialogs.messagebox',
            'sidepanel.reference.link.save',
            'bear.file.input',
            'ui.entity-selector',
            'slimselect',
            'calendar',
            'jquery',
            'date'
        ]);
        \Bitrix\Main\UI\Extension::load('ui.entity-selector');



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
