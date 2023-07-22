 <?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

 <div class="<?=$arResult['GRID_ID']?> item papers direction-docs">

     <div class="item-title position-relative">
         <img src="<?=$this->GetFolder().'/img/file2.svg'?>">
         <span>Письма, приказы, распоряжения</span>
         <div class="poisk-container" style="margin-right: 70px">
             <div class="search-icon">
                 <svg width="30px" class="svg-icon search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7"><title id="title"></title><g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"/><circle cx="8" cy="8" r="7"/></g></svg>
             </div>
            <input type="text" value="<?=$arParams['docs_like']?>" onchange="findDoc(this)"  class="finder">
         </div>


         <div cool-edit-here >
             <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
                 <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
             <?endif;?>
             <div cool-edit-btn data-action-reload="true" data-type="script" data-action="readMore" data-script="location.href='<?=$arResult['LIST_LINK']?>';"></div>
         </div>
     </div>

     <div class="normatives">
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
 

 <script>
     window.docs_editor = new CoolEditor({
         component : 'rs:activity.directions.item.docs.preview',
         componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
         view : 2
     });
 </script>
