<?php
use Bitrix\Main\Context;

\Bitrix\Main\UI\Extension::load("ui.buttons");
\Bitrix\Main\UI\Extension::load("ui.buttons.icons");
CJSCore::Init(['amcharts', 'spotlight']);
global $USER;
$userId = $USER->getId();
?>

<div class="<?=$arResult['GRID_ID']?>">
    <div class="row">
        <div class="col-3">
            <div class="left ico">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px" viewBox="0 0 24 24" version="1.1">
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="ic-lamp" fill-rule="nonzero" fill="#4A4A4A">
                            <path d="M14,15 L14,15 C14,15 17,10.7179603 17,8.86111111 C17,6.17639358 14.7614237,4 12,4 C9.23857625,4 7,6.17639358 7,8.86111111 C7,11.5458286 10,15 10,15 L14,15 Z M14,17 L10,17 L9.08801644,17 L8.49000407,16.3114543 C8.05227388,15.8074559 7.42989845,14.9980309 6.80434805,13.9978738 C6.16962621,12.983053 5.66962614,11.9751664 5.35561966,10.9828868 C5.12578781,10.2566042 5,9.55015675 5,8.86111111 C5,5.0590187 8.14641923,2 12,2 C15.8535808,2 19,5.0590187 19,8.86111111 C19,9.88033254 18.5978164,11.0183796 17.8910708,12.4133336 C17.6926413,12.8049882 17.4721551,13.2098586 17.2332398,13.6248118 C16.8847181,14.2301317 16.5138507,14.8283525 16.1430473,15.395869 C15.9195215,15.7379762 15.7434996,15.9970025 15.6380014,16.147585 L15.0407989,17 L14,17 Z M10,20 C9.44771525,20 9,19.5522847 9,19 C9,18.4477153 9.44771525,18 10,18 L14,18 C14.5522847,18 15,18.4477153 15,19 C15,19.5522847 14.5522847,20 14,20 L10,20 Z M11,23 C10.4477153,23 10,22.5522847 10,22 C10,21.4477153 10.4477153,21 11,21 L13,21 C13.5522847,21 14,21.4477153 14,22 C14,22.5522847 13.5522847,23 13,23 L11,23 Z M11.25,6 C10.9564596,6 10.5686937,6.06462764 10.1645898,6.26667961 C9.4501558,6.62389661 9,7.29913031 9,8.25 C9,8.66421356 9.33578644,9 9.75,9 C10.1642136,9 10.5,8.66421356 10.5,8.25 C10.5,7.88836969 10.6123442,7.71985339 10.8354102,7.60832039 C10.9938063,7.52912236 11.1685404,7.5 11.25,7.5 C11.6642136,7.5 12,7.16421356 12,6.75 C12,6.33578644 11.6642136,6 11.25,6 L11.25,6 Z" id="Combined-Shape">

                            </path>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <div class="col-5 secondary-text">
            Привет!<br> Здесь можно работать совместно с коллегами в рамках созданного Проекта, заводить Группы и добавлять Участников. Сохраняйте документы, ведите обсуждение на форумах, отслеживайте задачи! <br>Успехов в работе!
        </div>


    </div>

    <div class="row">
        <div class="col-6">
            <div class="block-title headhead  position-relative">
                <span class="position-relative">Список проектов

                    <?if($USER->IsAuthorized()):?>

                        <div cool-edit-here >
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['PROJECT_CREATE_LINK']?>"></div>
                        </div>
                    <?endif?>
                </span>


            </div>

            <div class="project-list ui-list">
                <?foreach ($arResult['PROJECTS'] as $project):?>
                    <div class="row" data-isnew="<?=$project['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">
                        <a class="table-data col-7" href="<?=$project['LINK']?>"><?=$project['TITLE']?></a>
                        <div class="table-data col-2"><?=$project['CREATED_TIME'] ? $project['CREATED_TIME']->format('d.m.Y') : ''?></div>
                        <div class="table-data col-3 position-relative">
                            <?=$project['LAST_NAME']?> <?=substr($project['NAME'], 0, 2)?>.<?=substr($project['SECOND_NAME'], 0, 2)?>.

                            <?if(array_intersect(['X'], $arResult['PERMISSIONS'][$project['ID']]) || (array_intersect(['W'], $arResult['PERMISSIONS'][$project['ID']]) && $project['CREATED_BY'] == $userId)):?>

                                <div cool-edit-here >
                                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$project['EDIT_LINK']?>"></div>
                                    <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$project['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ProjectTable')"></div>
                                </div>

                            <?endif;?>

                        </div>


                    </div>
                <?endforeach;?>

                <?if(!$arResult['PROJECTS']):?>
                    <div class="empty-informer">
                        <div class="empty-image"></div>
                        <div calss="empty-text">Нет данных</div>
                    </div>
                <?endif;?>

            </div>
        </div>
    </div>
</div>

<script>
    new CoolEditor({
        component : 'rs:project.list',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>

