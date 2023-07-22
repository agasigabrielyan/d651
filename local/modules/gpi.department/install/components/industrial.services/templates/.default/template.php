<?php
$this->addExternalCss($templateFolder.'/style2.css');
$this->addExternalJs($templateFolder.'/question-script.js');
CUtil::InitJSCore(['jquery3.6.1']);
\Bitrix\Main\UI\Extension::load("ui.hint");

?>
<div class="ds">

    <?if($arResult['FAVORITE_SERVICES'] || 'razreshily' == 'razreshily'):?>
        <div class="dsFavourite">
            <div class="dsFavourite__title">Избранное</div>
            <div class="servises-items">

                        <?foreach($arResult["FAVORITE_SERVICES"] as $arItem):?>
                            <div class="servises-item" >

                                <div class="dsServicesItem__star">
                                    <button class="dsStar" onclick="unFavorIt(<?=$arItem['ID']?>)">
                                    </button>
                                </div>
                                <div class="services-item__bg">
                                </div>

                                <div class="services-item__text">
                                    <?if($arItem["SERVICE_LINK"]):?>
                                        <a href="<?=$arItem["SERVICE_LINK"];?>" target="_blank" style="color: #1f4e79; color: #fff;">
                                            <?=$arItem['NAME']?>
                                        </a>
                                    <?else:?>
                                        <?=$arItem['NAME']?>
                                    <?endif;?>
                                </div>
                            </div>
                        <?endforeach;?>

                        <?
                        $itemsCount = count($arResult["FAVORITE_SERVICES"]) ;
                        while($itemsCount<6):$itemsCount++?>

                            <div class="servises-item ghost" >
                                <div class="services-item__bg">

                                    <lord-icon
                                            src="<?=$templateFolder.'/question.json'?>"
                                            trigger="hover"
                                            colors="primary:#fff">
                                    </lord-icon>

                                    <span>Добавьте<br> в избранное</span>

                                </div>
                            </div>

                        <?endwhile;?>

            </div>
        </div>

    <?endif;?>

    <div action="" class="dsSearchSecond">
       <div class="row">
           <div class="col-6 d-flex">
               Список А-Я


               <input class="digitalSearchSecond" type="text" name="search" value="" autocomplete="off">
               <div class="search-icon">
                   <svg width="30px" class="svg-icon search-icon top-search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7">
                       <title id="title"></title>
                       <g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"></path><circle cx="8" cy="8" r="7"></circle></g></svg>
               </div>

           </div>

           <div class="col-6">
               
           </div>
       </div>
    </div>

    <div class="dsServices">
        <div class="dsServices__column">
            <div class="dsServicesGroup__list">

                <?foreach ($arResult['SERVICES'] as $item):?>

                    <div class="dsServicesItem item<?=$item['ID']?><?=$item['IS_NEW'] ==1? ' new' : ''?>">
                        <a class="hlink" href="<?=$item['SERVICE_LINK']?>">
                            <div class="dsServicesItem__title"><?=str_replace('<br>', '', $item['NAME'])?></div>
                        </a>
                        <span data-hint-html data-hint='<?=$item['PREVIEW_TEXT']?>'></span>
                        <div class="dsServicesItem__star">
                            <button class="dsStar <?=$item['IS_FAVORITE'] == true? '' : 'active'?>" onclick="<?=$item['IS_FAVORITE'] == true? 'unFavorIt' : 'favorIt'?>(<?=$item['ID']?>)">
                            </button>
                        </div>

                    </div>

                <?endforeach;?>


            </div>
        </div>
    </div>
</div>