<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    \Bitrix\Main\Loader,
    Bitrix\Crm\Service\Container,
    Bitrix\Main\Engine\Contract\Controllerable;



class ThemeSelector extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    function setThemeAction($themeCode){
        global $USER;

        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('UF_THEME_RS', $themeCode);

        $user = new CUser;
        echo $user->Update($USER->getId(), ["UF_THEME_RS" => $themeCode]);
    }

    public function executeComponent() {

        global $USER;

        $userData = Bitrix\Main\UserTable::getList([
            'filter' => [
                'ID' => $USER->getId()
            ],
            'select' => [
                'UF_THEME_RS'
            ]
        ])->fetch();

        $this->arResult['UF_THEME'] = $userData['UF_THEME_RS'];

        global $APPLICATION;

        $APPLICATION->addHeadString('<script> window.userSelectedTheme = "'.$userData['UF_THEME'].'"</script>');

        $this->IncludeComponentTemplate();
    }

}
