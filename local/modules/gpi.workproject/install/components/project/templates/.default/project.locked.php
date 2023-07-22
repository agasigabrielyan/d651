<?php
use Gpi\Workproject\Orm\Entity\UrlManager;
if(count($arParams['USER_PERMISSIONS'])>0)
    header("Location: ".UrlManager::getProjectLockedLink($arParams['VARIABLES']['project_id']));
?>


<div class="locked" style="display: flex; align-items: center; flex-direction: column; height: calc(100% - 158px); justify-content: center;">

    <div class="title" style="display: block; font-style: normal; font-weight: 400; font-size: 27px; color: #5b78b2; line-height: 25px; font-family: HeliosCondRegular;">
        Проект не найден или доступ ограничен
    </div>
    <svg width="20%" xmlns="http://www.w3.org/2000/svg" id="Layer_1" enable-background="new 0 0 512 512" viewBox="0 0 512 512">
        <g>
            <g>
                <g>
                    <path d="m192.2 227.7h-45.8l-23 39.7 23 39.8h45.8l23-39.8z" fill="#7b7be0" />
                    <path d="m261.1 267.4h-45.9l-23 39.8 23 39.7h45.9l23-39.7z" fill="#f4f4f4" />
                    <g fill="#7b7be0">
                        <path d="m330 227.7h-45.9l-23 39.7 23 39.8h45.9l22.9-39.8z" />
                        <path d="m123.4 346.9h-45.9l-22.9 39.7 22.9 39.8h45.9l23-39.8z" />
                    </g>
                </g>
                <g>
                    <path d="m123.4 267.4h-45.9l-21.7 37.6h-25.9c-.8 0-1.5.4-1.9 1.1l-10.6 20.3 3.7 2 10-19.1h24.7l21.8 37.6h45.9l23-39.7z" fill="#f4f4f4" />
                    <circle cx="17.5" cy="329.2" fill="#bbd8ff" r="6.7" />
                </g>
            </g>
            <g>
                <g>
                    <path d="m405.6 192.2h-300.9c-5.3 0-9.5 4.3-9.5 9.5v192.8c0 5.3 4.3 9.5 9.5 9.5h300.9c5.3 0 9.5-4.3 9.5-9.5v-192.9c-.1-5.1-4.4-9.4-9.5-9.4z" fill="#709acf" />
                    <path d="m401.3 386.3h-292.3c-1.3 0-2.4-1.1-2.4-2.4v-177.9c0-1.3 1.1-2.4 2.4-2.4h292.3c1.3 0 2.4 1.1 2.4 2.4v177.9c-.1 1.3-1.1 2.4-2.4 2.4z" fill="#fdfdff" />
                    <g>
                        <path d="m361.7 247h-213.2c-6.8 0-12.3-5.5-12.3-12.3 0-6.8 5.5-12.3 12.3-12.3h213.1c6.8 0 12.3 5.5 12.3 12.3 0 6.9-5.4 12.3-12.2 12.3z" fill="#bbd8ff" />
                        <g>
                            <path d="m361.7 222.5h-19.1v24.6h19.1c6.8 0 12.3-5.5 12.3-12.3-.1-6.8-5.5-12.3-12.3-12.3z" fill="#7399f6c4" />
                            <path d="m365.5 239.2-5.1-4.3c.4-.8.7-1.6.7-2.6 0-3.3-2.6-6-6-6-3.3 0-6 2.6-6 6s2.6 6 6 6c1.3 0 2.5-.4 3.5-1.2l5.1 4.3zm-10.5-3.8c-1.8 0-3.2-1.4-3.2-3.2s1.4-3.2 3.2-3.2 3.2 1.4 3.2 3.2-1.4 3.2-3.2 3.2z" fill="#fdfdff" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <g>
                                <circle cx="157.9" cy="288.8" fill="#709acf" r="22.9" />
                                <g>
                                    <path d="m157.9 289.9c-8 0-14.4 6.5-14.4 14.4v2.3c3.9 3.2 9 5.1 14.4 5.1 5.5 0 10.5-2 14.4-5.1v-2.3c0-7.9-6.4-14.4-14.4-14.4z" fill="#b1c8ef" />
                                    <circle cx="157.9" cy="286" fill="#bbd8ff" r="8.9" />
                                </g>
                            </g>
                            <g fill="#709acf">
                                <path d="m209.8 300.2h159.2v4.3h-159.2z" />
                                <path d="m209.8 273.4h159.2v4.3h-159.2z" />
                            </g>
                        </g>
                        <g>
                            <g>
                                <circle cx="157.9" cy="344.4" fill="#709acf" r="22.9" />
                                <g>
                                    <path d="m157.9 345.4c-8 0-14.4 6.5-14.4 14.4v2.3c3.9 3.2 9 5.1 14.4 5.1 5.5 0 10.5-2 14.4-5.1v-2.3c0-8-6.4-14.4-14.4-14.4z" fill="#b1c8ef" />
                                    <circle cx="157.9" cy="341.6" fill="#bbd8ff" r="8.9" />
                                </g>
                            </g>
                            <g fill="#709acf">
                                <path d="m209.8 355.7h159.2v4.3h-159.2z" />
                                <path d="m209.8 328.9h159.2v4.3h-159.2z" />
                            </g>
                        </g>
                    </g>
                </g>
                <g>
                    <path d="m289.3 450.6h-68.4l7-46.7h54.4z" fill="#b1c8ef" />
                    <path d="m226.6 412.8h57l-1.3-8.9h-54.4z" fill="#7399f6c4" />
                </g>
            </g>
            <g>
                <path d="m269.9 105h-28.3l-14.3 24.5 14.3 24.6h28.3l14.2-24.6z" fill="#7b7be0" />
                <path d="m227.3 80.4h-28.3l-14.2 24.6 14.2 24.5h28.3l14.3-24.5z" fill="#bbd8ff" />
                <path d="m269.9 55.8h-28.3l-14.3 24.6 14.3 24.6h28.3l14.2-24.6z" fill="#f4f4f4" />
            </g>
            <g>
                <path d="m446.9 52h13.8v394.4h-13.8z" fill="#bbd8ff" />
                <g>
                    <path d="m282.6 72.9-23.8 40.2v25.6h4.4l48-65.8z" fill="#709acf" />
                    <path d="m311.2 72.9-48 65.8h30l48-65.8z" fill="#b1c8ef" />
                    <path d="m341.2 72.9-48 65.8h31.8l48-65.8z" fill="#709acf" />
                    <path d="m495.2 138.7v-25.4l-16.3 25.4z" fill="#709acf" />
                    <path d="m258.8 72.9v40.2l23.8-40.2z" fill="#b1c8ef" />
                    <path d="m436.5 72.9-47.9 65.8h31.7l48-65.8z" fill="#b1c8ef" />
                    <path d="m495.2 72.9h-26.9l-48 65.8h30.1l44.8-62.4z" fill="#709acf" />
                    <path d="m450.4 138.7h28.5l16.3-25.4v-37z" fill="#b1c8ef" />
                    <path d="m404.8 72.9-48 65.8h31.8l47.9-65.8z" fill="#709acf" />
                    <path d="m373 72.9-48 65.8h31.8l48-65.8z" fill="#b1c8ef" />
                </g>
            </g>
            <g>
                <path d="m434.2 350.1h-4.3v-172.7h-262.3c-1.2 0-2.1-1-2.1-2.1v-121.6h-125.8v-4.3h127.8c1.2 0 2.1 1 2.1 2.1v121.7h262.3c1.2 0 2.1 1 2.1 2.1v174.8z" fill="#f4f4f4" />
                <circle cx="36.5" cy="51.2" fill="#bbd8ff" r="7.1" />
            </g>
            <g>
                <g>
                    <g>
                        <path d="m11.8 377.4h170v88.3h-170z" fill="#fff" />
                        <path d="m38.6 443.5h116.4v11.7h-116.4z" fill="#f4f4f4" />
                        <g>
                            <path d="m86.4 417.5c-4.3 7.4 1.1 16.7 9.7 16.7 8.6 0 14-9.3 9.7-16.7-4.3-7.5-15-7.5-19.4 0z" fill="#7399f6c4" />
                            <g>
                                <path d="m94.4 429.2c0-1.1.8-1.9 1.8-1.9 1.1 0 1.8.8 1.8 1.9 0 1-.7 1.9-1.8 1.9-1-.1-1.8-.8-1.8-1.9zm.8-4.2-.4-14.1h2.8l-.5 14.1z" fill="#bbd8ff" />
                            </g>
                        </g>
                    </g>
                </g>
                <g>
                    <path d="m467.7 341.2h-71.9l-35.9 62.3 35.9 62.2h71.9l36-62.2z" fill="#fff" />
                    <path d="m402.5 454.2-29.3-50.7 29.3-50.8h58.6l29.2 50.8-29.2 50.7z" fill="#b1c8ef" />
                    <path d="m453.8 380.2c-1.9 0-3.5 1.5-3.5 3.5v20.9c0 1-.8 1.9-1.8 1.9s-1.9-.8-1.9-1.8v-28.7c0-2.1-1.8-3.8-3.8-3.8-2.1 0-3.8 1.8-3.8 3.8v27.9c0 1-.8 1.9-1.8 1.9s-1.9-.8-1.9-1.8v-33.5c0-2-1.5-3.5-3.5-3.5s-3.5 1.5-3.5 3.5v33.3c0 1-.8 1.9-1.8 1.9s-1.9-.8-1.9-1.8v-29c0-2.2-1.9-3.7-3.9-3.5-1.9 0-3.5 1.5-3.5 3.5v27.5c0 .7-.3 1.3-1 1.6-.9.4-1.8.1-2.3-.7l-4.7-7.3c-.9-1.9-3.3-2.6-5-1.6-1.8 1-2.4 3.2-1.6 4.9l5.9 11.2c0 .1.1.1.1.2l5.4 14c2.8 7.4 10.1 12.4 17.9 12.4h11.1c8 0 14.6-6.6 14.6-14.6v-38.2c.1-2.4-1.6-4.1-3.8-4.1z" fill="#fff" />
                </g>
            </g>
            <g>
                <g>
                    <path d="m34.1 68.5h116.6v146.5h-116.6z" fill="#b1c8ef" />
                    <g fill="#f4f4f4">
                        <path d="m49.7 82.7h85.4v12.4h-85.4z" />
                        <path d="m50.9 107.7h22.8v22.8h-22.8z" />
                        <path d="m89.9 109.3h40.7v4.3h-40.7z" />
                        <path d="m89.9 123.7h40.7v4.3h-40.7z" />
                        <path d="m52.8 140.3h77.9v4.3h-77.9z" />
                        <path d="m52.8 155.1h77.9v4.3h-77.9z" />
                        <path d="m52.8 169.6h77.9v4.3h-77.9z" />
                        <path d="m52.8 184h77.9v4.3h-77.9z" />
                        <path d="m52.8 198.6h77.9v4.3h-77.9z" />
                    </g>
                </g>
                <g>
                    <g>
                        <path d="m8.3 183.8 34.2-10.7 35.4 9.5.1 7.7c.2 23.8-13.1 45.8-34.4 56.8-20.9-10.4-34.5-31.5-35.1-54.8z" fill="#fff" />
                        <path d="m43.7 249.1c-.3 0-.7-.1-1-.2-21.3-10.4-35.8-33-36.3-56.7l-.2-8.4c0-1 .5-1.8 1.5-2.1l34.2-10.7c.4-.1.8-.1 1.2 0l35.4 9.5c.9.2 1.5 1.1 1.5 2.1l.1 7.7c.4 24.3-13.9 47.8-35.6 58.6-.1.2-.5.2-.8.2zm-33.2-63.8.2 6.8c.7 22.2 13.2 42.2 32.9 52.5 20.1-10.7 32.5-31.5 32.3-54.4v-6l-33.3-9z" fill="#709acf" />
                    </g>
                    <g>
                        <circle cx="43.2" cy="205.2" fill="#709acf" r="15.5" />
                        <path d="m35.3 203.2h15.3v4.3h-15.3z" fill="#f4f4f4" />
                    </g>
                </g>
            </g>
            <path d="m471.4 463.6 34.1-59.1c.3-.7.3-1.4 0-2.1l-36-62.2c-.3-.7-1.1-1.1-1.9-1.1h-71.9c-.8 0-1.4.4-1.9 1.1l-35.9 62.2c-.3.7-.3 1.4 0 2.1l34.1 59.1h-74.1c-1-7.3-7.3-13-15-13h-96.3c-7.7 0-13.9 5.7-15 13h-8v-86.2c0-1.2-1-2.1-2.1-2.1h-170c-1.2 0-2.1 1-2.1 2.1v86.2h-9.4v4.3h512v-4.3zm-291.7 0h-165.8v-69.8h165.9v69.8zm182.6-60.1 34.7-60.2h69.4l34.7 60.2-34.7 60.2h-69.3z" fill="#709acf" />
            <g fill="#f4f4f4">
                <circle cx="169.6" cy="384.9" r="2.5" />
                <circle cx="159" cy="384.9" r="2.5" />
                <circle cx="148.3" cy="384.9" r="2.5" />
            </g>
        </g>
    </svg>

</div>