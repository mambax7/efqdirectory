<?php

use Xmf\Request;

require_once __DIR__ . '/common/traitversionchecks.php';
require_once __DIR__ . '/common/traitserverstats.php';
require_once __DIR__ . '/common/traitfilesmgmt.php';

require_once __DIR__ . '/../include/common.php';

/**
 * Class EfqDirectoryUtility
 */
class EfqDirectoryUtility extends XoopsObject
{
    use VersionChecks; //checkVerXoops, checkVerPhp Traits

    use ServerStats; // getServerStats Trait

    use FilesManagement; // Files Management Trait
}
