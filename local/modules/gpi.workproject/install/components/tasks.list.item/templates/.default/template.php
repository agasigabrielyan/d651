<?php
$APPLICATION->setTitle("Задача {$arResult['TASK']['TITLE']}");
global $USER;
switch (MAIN_USER_THEME) {
    case 'WHITE':
        $APPLICATION->SetAdditionalCSS($this->GetFolder() . '/white_theme.css');
        break;
}
?>

<div class="task">

    <div class="description">
        <?=$arResult['TASK']['DESCRIPTION']?>
    </div>

    <?foreach($arResult['STRUCTURE'] as $struct):?>

        <?if(!$arResult['TASK'][$struct['CODE']]){continue;}?>

        <?if($struct['CODE'] == 'FILES'):?>
            <div data-code="<?=$struct['CODE']?>" class="field">
                <span class="field-label"><?=$struct['LABEL']?>: </span>

                <div class="field-val d-flex flex-column">

                    <?foreach ($arResult['TASK'][$struct['CODE']] as $file):?>

                        <a href="<?=$file['LINK']?>"  target="_blank"> <?=$file['TITLE']?></a>
                    <?endforeach;?>

                </div>
            </div>
        <?elseif($struct['CODE'] == 'PROVIDER' || $struct['CODE'] == 'PRODUCER'):?>
            <div data-code="<?=$struct['CODE']?>" class="field">
                <span class="field-label"><?=$struct['LABEL']?>: </span>

                <div class="field-val">
                    <?
                    $APPLICATION->IncludeComponent('rs:user.preview', '', [
                        'PREVIEW' => $arResult['TASK'][$struct['CODE']."_PREVIEW"],
                        'FULL_NAME' => $arResult['TASK'][$struct['CODE'].'_FULL_NAME'],
                        'ID' => $arResult['TASK'][$struct['CODE']],
                    ])
                    ?>
                </div>
            </div>
        <?else:?>
            <div data-code="<?=$struct['CODE']?>" class="field">
                <span class="field-label"><?=$struct['LABEL']?>: </span>

                <div class="field-val"> <?=$arResult['TASK'][$struct['CODE']]?> </div>
            </div>
        <?endif;?>
    <?endforeach;?>



    <?
    $APPLICATION->IncludeComponent('rs:entity.comments', '', [
        'ENTITY' => 'TASK',
        'ENTITY_ID' => $arResult['TASK']['ID'],
        'USER_PERMISSIONS' => $arParams['USER_PERMISSIONS']
    ])
    ?>
</div>