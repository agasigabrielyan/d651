<?php
namespace Gpi\Workproject\Helper;

use \Bitrix\Main\Application;

class FormData
{

    public static function parse($dateTimeFields = [], $setMemberChanges=0){
        $request = Application::getInstance()->getContext()->getRequest();
        $data = $request->getPostList()->toArray();
        $files = $request->getFileList()->toArray();
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

        foreach ($data as $key => $value){

            if($data[$key.'_IS_MULTIPLE_STRING'] == 'Y'){
                unset($data[$key.'_IS_MULTIPLE_STRING']);
                $data[$key] = explode(',', $data[$key]);
            }
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
            return json_encode([
                'status' => 1,
                'elemenId' => $addResult->getID(),
            ]);
        else
            return json_encode([
                'status' => 0,
                'error' => 'Ошибка: ' . implode(', ', $addResult->getErrors()),
            ]);
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

}