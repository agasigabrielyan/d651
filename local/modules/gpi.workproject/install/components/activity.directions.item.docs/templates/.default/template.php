 <?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->setTitle('Письма, приказы, распоряжения');

 ?>

 <div class="<?=$arResult['GRID_ID']?>">
     <div class="direction-text" style="font-family: system-ui">
         <b class="direction-label">Детальное направление:</b> <a onclick="" href="<?=$arParams['DIRECTION_LINK']?>" class="direction-link"><?=$arParams['DIRECTION_DATA']['TITLE']?></a>
     </div>

     <div class=" item papers direction-docs activity_direction">

         <div class="poisk-container ml-auto d-flex">

             <div class="search-icon">
                 <svg width="30px" onclick="findDocs(this.closest('.poisk-container').querySelector('input'))" class="svg-icon search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7"><title id="title"></title><g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"></path><circle cx="8" cy="8" r="7"></circle></g></svg>
             </div>
             <input type="text" value="<?=$arParams['docs_like']?>" data-entity="iblock" onchange="findDocs(this);" class="finder">
         </div>

         <div class="normatives position-relative" style="padding-top: 50px">

             <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
                 <div cool-edit-here>
                     <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
                 </div>
             <?endif;?>

             <div class="normatives-list">
                 <div class="ui-table">
                     <?foreach($arResult["RULLS"] as $arItem):?>

                         <div class="table-row editor-container row" data-new-id="<?=$arItem['NEW_ID']?>" data-isnew="<?=$arItem['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">
                             <a class="table-coll col-7" href="<?=$arItem['FILE_PATH']?>" download="<?=$arItem['TITLE']?>.<?=pathinfo($arItem['FILE_PATH'])['extension']?>" target="_blank" onclick="showBXFileRS(<?=$arItem['FILE_ID']?>)">
                                 <?=$arItem['TITLE']?>
                            </a>

                             <div class="table-coll col-2">
                                 <?= $arItem['ORDER_DATE'] ? date('d.m.Y', strtotime($arItem['ORDER_DATE'])) : ''?>
                             </div>

                             <div class="table-coll col-3">
                                 <?= $arItem['ORDER_NUMBER']?>
                             </div>

                             <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
                                 <div cool-edit-here >
                                     <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arItem['EDIT_LINK']?>"></div>
                                     <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arItem['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionOrderTable')"></div>
                                 </div>
                             <?endif;?>
                         </div>
                     <?endforeach;?>

                 </div>
             </div>
         </div>


     </div>

 </div>

 <script>
     window.docs_editor = new CoolEditor({
         component : 'rs:activity.directions.item.docs',
         componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
         view : 2
     });
 </script>
