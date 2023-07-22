<?

$shadowBlocks = 6-count(is_array($arResult['FAVORITE_SERVICES'])? $arResult['FAVORITE_SERVICES']: []);

?>
<section class='digital-services'>
    <div class='zag-but'>
        <div class='zag-block'>
            <h2>Цифровые сервисы</h2>
        </div>
        <a href='/industry_services/' class='but-block'>Все сервисы</a>
    </div>
    <section class='all-system'>
        <?
        foreach($arResult['FAVORITE_SERVICES'] as $itemCode => $itemArray){?>
            <article class='one-system'>
                <a href='<?=$itemArray['FAVORITE_SERVICES']?>'>
                    <div class='pic-system'><img style="max-width: 45px;" src='<?=$itemArray['LINK']?>' alt='' /></div>
                    <div class='name-system'><?=$itemArray['NAME']?></div>
                </a>
            </article>
        <?}?>
        <?while($i<$shadowBlocks){$i++;?>
            <article class='one-system blured'>
                <a href='/industry_services/'>
                    <div class='pic-system'><img style="max-width: 45px;"src='' alt='' /></div>
                    <div class='anons-system'><span>Выберите</span> <span>цифровой</span> <span>сервис</span></div>
                </a>
            </article>
        <?}?>
    </section>
</section>