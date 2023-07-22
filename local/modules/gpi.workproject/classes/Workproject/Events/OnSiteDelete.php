<?php
namespace Gpi\Workproject\Events;

use Gpi\Workproject\Orm,
    Gpi\Workproject\Entity;

class OnSiteDelete
{
    function Listen(&$siteId)
    {
        Orm\DepartmentTable::deleteBySiteId($siteId);
    }
}