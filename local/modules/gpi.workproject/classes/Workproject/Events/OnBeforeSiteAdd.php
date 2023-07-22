<?php
namespace Gpi\Workproject\Events;

use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity;

class OnBeforeSiteAdd
{
    function Listen(&$arFields)
    {
        Orm\DepartmentTable::add([
            'SITE_ID' => $arFields['LID'],
        ]);
    }
}