<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<?php
CJSCore::init(['jquery3.6.1', 'owl.carousel', 'fancybox']);
?>

<div class="gallery">
    <div class="block-title">
    <span>Галерея
    </span>
    </div>
    <div class="owl-carousel owl-theme galleryimg">
        <?foreach($arResult["PHOTOS"] as $arItem):?>
            <a data-fancybox="gallery" href="<?=$arItem['PICTURE_PATH']?>" data-caption="<?=$arItem['TITLE']?>">
                <img class="gallery-lightbox" src="<?=CFile::ResizeImageGet($arItem['FILE'], ["width" => 640 , "height" => 500], BX_RESIZE_IMAGE_EXACT , true)['src']?>" alt="">
            </a>
        <?endforeach;?>
    </div>

    <div class='rs-owl-nav'>
        <a class="customServicesOwlPrev" href="javascript:void(0);" style='width: 32px;'>
            <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" id="Layer_1" viewBox="0 0 100 100"><title/><polygon points="35 20 35 35 50 50 35 65 35 80 65 50 35 20"/></svg>
        </a>
        <a class="customServicesOwlNext" href="javascript:void(0);" style='width: 32px;'>
            <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" id="Layer_1" viewBox="0 0 100 100"><title/><polygon points="35 20 35 35 50 50 35 65 35 80 65 50 35 20"/></svg>
        </a>
    </div>


</div>



