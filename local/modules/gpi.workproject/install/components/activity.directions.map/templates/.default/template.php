<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$selectedItem = $arResult['activeDirection'];
global $USER;
$userId = $USER->getId();
?>
<div class="activity-direction-index">


    <div class="row">
        <div class="block-title"><span>Направления деятельности</span></div>


        <div class="col-5">

            <div class="direction" style='position: relative;'>
                <div class="direction-text"><span>Н</span>аправления</div>
                <div class="canvas">

                    
                    <div cool-edit-here>
                        <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['MAIN_ADD_LINK']?>"></div>
                        <?endif;?>
                        <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="settings" data-link="<?=$arResult['SETTINGS']?>"></div>
                        <?endif;?>
                    </div>
                    

                    <svg class="chart" width="400" height="400" viewBox="0 0 50 50">

                        <?foreach($arResult["MAIN_DIRECTIONS"] as $arItem):?>

                            <circle id="circle<?=$arItem['ID']?>" class="unit <?if($selectedItem == $arItem['ID']):?> active <?endif;?>" data-id="<?=$arItem['ID']?>" r="15.9" cx="50%" cy="50%" onclick="showTargetLinksD7(this)" data-curator='<?=implode(',', $arItem['CURATORS'])?>'></circle>

                        <?endforeach;?>


                        <?foreach(array_filter($arResult["MAIN_DIRECTIONS"], function($v) use($selectedItem){
                            return $v['ID'] == $selectedItem;
                        }) as $arItem):?>

                            <use xlink:href="#circle<?=$arItem['ID']?>" class="slink"/>

                        <?endforeach;?>



                    </svg>

                    <div>
                        <?foreach($arResult["MAIN_DIRECTIONS"] as $arItem):?>

                            <div class="caption-item <?=$arItem['IS_NEW']? 'new': ''?> <?if($selectedItem == $arItem['ID']):?> active <?endif;?>" onclick="showTargetLinksD7(this)" data-id="<?=$arItem['ID']?>" data-curator='<?=implode(',', $arItem['CURATORS'])?>'>

                                <?if((array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $arItem['CREATED_BY'] == $userId) || array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                                    <div cool-edit-here>
                                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arItem['EDIT_LINK']?>"></div>
                                        <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arItem['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ActivityDirectionTable')"></div>
                                    </div>
                                <?endif;?>
                                <?=$arItem['DESCRIPTION']?>
                            </div>
                        <?endforeach;?>
                    </div>


                </div>

                <div class="activity-text"><?=implode(',', $arResult['ACTIVE_DIRECTION']['CURATORS'])?></div>
                
            </div>

        </div>

        <div class="col-7 position-relative">


            <div class="desk-block position-relative">

                <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
                    <div cool-edit-here>
                        <div cool-edit-btn data-action-reload="true" data-type="script" data-action="add" data-script="createDetailDirectionLink('<?=$arResult['DETAIL_ADD_LINK']?>', <?=$selectedItem?>)"></div>
                    </div>
                <?endif;?>

                <ul class="desk-block-list">
                    <?foreach($arResult["DETAIL_DIRECTIONS"] as $arItem):?>
                        <li class="<?=$selectedItem != $arItem["ACTIVITY_DIRECTION_ID"]? 'hidden' : ''?> <?=$arItem['IS_NEW']? 'new': ''?>" data-link-id="<?=$arItem["ACTIVITY_DIRECTION_ID"]?>">

                            <?if((array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $arItem['CREATED_BY'] == $userId) || array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                                <div cool-edit-here>
                                    <div data-action-reload="true" cool-edit-btn data-type="link" data-action="edit" data-link="<?=$arItem['EDIT_LINK']?>"></div>
                                    <div data-action-reload="true" cool-edit-btn data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arItem['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionTable')"></div>
                                </div>
                            <?endif;?>
                            <a style="color:#fff;" onclick="" href="<?=$arItem['LINK']?>"><?=$arItem['TITLE']?></a>
                            <svg class="before" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" height="800px" width="800px" version="1.1" id="Capa_1" viewBox="0 0 128.411 128.411" xml:space="preserve">
                                <g>
                                    <g>
                                        <polygon points="127.526,15.294 45.665,78.216 0.863,42.861 0,59.255 44.479,113.117 128.411,31.666   "/>
                                    </g>
                                </g>
                            </svg>
                        </li>
                    <?endforeach;?>

                </ul>


            </div>


            <div class="fixed-bottom position-absolute d-flex">
                <div class="direction-nav ml-auto" hidden>
                    <a class="prev dir-btn" href="javascript:void(0);" style="width: 32px;">
                        <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" id="Layer_1" viewBox="0 0 100 100"><title></title><polygon points="35 20 35 35 50 50 35 65 35 80 65 50 35 20"></polygon></svg>
                    </a>
                    <a class="next dir-btn" href="javascript:void(0);" style="width: 32px;">
                        <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" id="Layer_1" viewBox="0 0 100 100"><title></title><polygon points="35 20 35 35 50 50 35 65 35 80 65 50 35 20"></polygon></svg>
                    </a>
                </div>
                <a href="/activity/content/" class="ui-btn-sm light-btn ml-auto d-table"> Все публикации</a>
            </div>


        </div>
    </div>


</div>

<script>
    new CoolEditor({
        component: 'rs:activity.directions.map',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>



