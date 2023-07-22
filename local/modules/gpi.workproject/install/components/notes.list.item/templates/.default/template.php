

<div class="detal-element">


    <div class="row">
        <div class="col-12">
            <div class="primary-info">
                <div class="date"><?=$arResult['DATE']? $arResult['DATE']->format('d.m.Y') : ''?></div>
                <div class="title"><?=$arResult['TITLE']?></div>
            </div>

            <ul>
                <?foreach ($arResult['FILES'] as $file):?>
                    <li><a href="<?=$file['FILE_PATH']?>" download="<?=$file['ORIGINAL_NAME']?>"><?=$file['ORIGINAL_NAME']?></a></li>
                <?endforeach;?>
            </ul>
        </div>
    </div>

    <div class="detail-text">
        <?=$arResult['DESCRIPTION']?>
    </div>
</div>