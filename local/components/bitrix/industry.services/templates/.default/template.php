<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var TYPE_NAME $arResult */
/** @var TYPE_NAME $USER */
/** @var string $templateFolder */
?>

<?php
$this->addExternalCss($templateFolder.'/style2.css');
CUtil::InitJSCore(['jquery3.6.1', 'owl.carousel']);
?>
<script src="/local/lib/js/owl.carousel/owl.carousel.js"></script>


<div hidden class="favorites">
    <div class="favorites-items">

        <?for ($i = 0; $i <= 5; $i++):?>
            <a class="favorites-item" href="<?=$arResult["FAV_SERVICES"][$i]["URL"] ?? '#!';?>">
                <div calss="flag"></div> 
                <p><?=$arResult["FAV_SERVICES"][$i]["NAME"] ?? '';?></p>
            </a>
        <?endfor;?>

    </div>
    
</div>

<div class="servises-wrapper">
    <div class="servises-items owl-carousel owl-theme" >

        <?foreach($arResult["FAV_SERVICES"] as $arItem):?>
                <div class="servises-item" >
                    <div class="services-item__bg">
                    </div>

                    <div class="services-item__text">
                        <?if($arItem["URL"]):?>
                            <a href="<?=$arItem["URL"];?>" target="_blank" style="color: #1f4e79; color: #fff;">
                                <?=$arItem['NAME']?>
                            </a>
                        <?else:?>
                            <?=$arItem['NAME']?>
                        <?endif;?>
                    </div>
                </div>
        <?endforeach;?>

        <?
        $itemsCount = count($arResult["FAV_SERVICES"]) ;
        while($itemsCount<6):$itemsCount++?>

            <div class="servises-item ghost" >
                <div class="services-item__bg">
                    <!--span class="ghost-word"></span> <br><span class="ghost-word"></span>  <span class="ghost-word"> </span-->
                </div>
            </div>
            
        <?endwhile;?>

    </div>
</div>


<div class="container-fluid px-0">
    <div class="row w-100">
        <div class="col-6 pr-2">
            <div class="block-title headhead d-flex justify-content-between">
                <span>Список А-Я</span>

                <form class="search-form">
                    <div class="poisk-container-main">
                        <div class="search-icon">
                            <svg width="30px" class="svg-icon search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7"><title id="title"></title><g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"></path><circle cx="8" cy="8" r="7"></circle></g></svg>
                        </div>
                        <input id="search-input" class="search-input" type="search" name="search-input" value="" size="40" maxlength="50" autocomplete="off">&nbsp;<input hidden="" name="s" type="submit" value="Поиск">

                    </div>
                </form>
            </div>

            <div class="services-list">
                <?foreach($arResult["SERVICES"] as $arItem):?>

                    <div class="servise-item">

                        <p>
                            <?if($arItem["IS_FAVORITE"]):?>
                                <a class="service-text" href="javascript:void(0);">
                                    <img src="<?=$templateFolder?>/images/bookmark-info.png" width="20" height="20" alt="">
                                </a>                                &nbsp;
                            <?else:?>
                                <a class="service-text" href="javascript:void(0);">
                                    <img src="<?=$templateFolder?>/images/bookmark-info-nofav.png" width="20" height="20" alt="">
                                </a>
                            <?endif;?>

                            <a class="src-text" href="<?=$arItem["LINK_VALUE"];?>" target="_blank">
                                <?=$arItem["NAME"];?>
                            </a>

                        </p>
                        <span style="display: none" class="service-preview" >
                            <?=$arItem["PREVIEW_TEXT"];?>

                            <?if($arItem["IS_FAVORITE"]):?>

                                <a class="del_fav" data-flag="del"
                                   data-elid="<?=$arItem["ID"];?>" data-uid="<?=$USER->GetID();?>"
                                   data-url="<?=$arItem["LINK_VALUE"];?>" data-name="<?=$arItem["NAME"];?>" href="javascript:void(0);">
                                        <span>удалить из Избранного</span>
                                </a>

                                <a style="display: none" class="add_fav" data-flag="add"
                                   data-elid="<?=$arItem["ID"];?>" data-uid="<?=$USER->GetID();?>"
                                   data-url="<?=$arItem["LINK_VALUE"];?>" data-name="<?=$arItem["NAME"];?>" href="javascript:void(0);">
                                    <span>в Избранное</span>
                                </a>

                            <?else:?>

                                <a style="display: none" class="del_fav" data-flag="del"
                                   data-elid="<?=$arItem["ID"];?>" data-uid="<?=$USER->GetID();?>"
                                   data-url="<?=$arItem["LINK_VALUE"];?>" data-name="<?=$arItem["NAME"];?>" href="javascript:void(0);">
                                        <span>удалить из Избранного</span>
                                </a>

                                <a class="add_fav" data-flag="add"
                                   data-elid="<?=$arItem["ID"];?>" data-uid="<?=$USER->GetID();?>"
                                   data-url="<?=$arItem["LINK_VALUE"];?>" data-name="<?=$arItem["NAME"];?>" href="javascript:void(0);">
                                    <span>в Избраное</span>
                                </a>

                            <?endif;?>

                        </span>
                    </div>
                <?endforeach;?>
            </div>

        </div>
        <div class="col-6 pl-2">
            <div class="block-title headhead">
                <span>Краткое описание</span>
            </div>
            <div class="favorites-text"></div>

        </div>
    </div>
</div>