<?php
namespace Gpi\Workproject\Orm;

class Helper
{
    public static function setColumnShadowDate($tableName, $shadowCode, $entityId, $data){

        if(is_array($entityId) && $entityId['ID'])
            $entityId = $entityId['ID'];

        $issetDataRS = $tableName::getList([
            'filter' => [
                $shadowCode => $entityId
            ], 
            'select' => ['ID', 'VALUE']
        ])->fetchAll();
        
        $issetData = array_column($issetDataRS, 'VALUE');

        $valueIds =[];
        foreach ($issetDataRS as $item){
            $valueIds[$item['VALUE']] = $item['ID'];
        }

        $toDeleteVals = array_diff($issetData, $data);
        $toSaveVals = array_diff($data, $issetData);

        foreach ($toDeleteVals as $value){
            $tableName::delete($valueIds[$value]);
        }

        foreach ($toSaveVals as $value){
            $tableName::add([
                $shadowCode => $entityId,
                'VALUE' => $value
            ]);
        }
        
    }

    public static function unsetColumnShadowDate($tableName, $shadowCode, $entityId){
        $elsToDelete = $tableName::getList(['filter' => [$shadowCode => $entityId], 'select' => ['ID']])->fetchAll();

        foreach ($elsToDelete as $el)
            $tableName::delete($el['ID']);
    }
}